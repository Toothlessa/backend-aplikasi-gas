<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Log;

/**
 * TransactionService
 *
 * Bertanggung jawab atas business logic transaksi.
 * - Mengatur alur create & update transaksi
 * - Menjaga konsistensi data antara transaction dan stock
 * - Mengelola atomic operation (DB transaction)
 *
 * NOTE:
 * Controller hanya boleh memanggil service,
 * service yang mengatur alur dan validasi bisnis.
 */
class TransactionService
{
    protected $repository;
    protected $masterItemService;
    protected $stockItemService;
    protected $customerService;
    protected $receivableService;

    /**
     * Dependency Injection
     *
     * Service bergantung pada repository & service lain,
     * bukan langsung ke model, agar:
     * - mudah di-test
     * - mudah di-refactor
     * - single responsibility
     */
    public function __construct(
        TransactionRepository $repository,
        MasterItemService $masterItemService,
        StockItemService $stockItemService,
        CustomerService $customerService,
        ReceivableService $receivableService,
    ) {
        $this->repository = $repository;
        $this->masterItemService = $masterItemService;
        $this->stockItemService = $stockItemService;
        $this->customerService = $customerService;
        $this->receivableService = $receivableService;
    }

    /**
     * Create new transaction
     *
     * This process must be atomic (all or nothing):
     * - create stock
     * - generate trx number
     * - create transaction
     */
    public function autoCreateTransaction($data)
{
    Log::info('TransactionService:autoCreateTransaction started', [
        'item_id'           => $data['item_id'],
        'customer_id'       => $data['customer_id'],
        'quantity'          => $data['quantity']
    ]);

    try {

        return DB::transaction(function () use ($data) {

            # validate data existence
            $masterItem = $this->masterItemService->findById($data['item_id']);
            $customer   = $this->customerService->getCustomerById($data['customer_id']);

            Log::debug('TransactionService:master data loaded', [
                'master_item_id'    => $masterItem->id,
                'customer_id'       => $customer->id
            ]);

            /**
             * Transform data stock
             * - Sales → stock decrease (minus)
             */
            # master
            $customerId             = $customer->id;
            $itemId                 = $masterItem->id;
            $cogs                   = $masterItem->cost_of_goods_sold;
            # data
            $salesQuantity          = $this->resolveStockDirection(TransactionType::SALES, $data['quantity']);
            $transactionQuantity    = $this->resolveStockDirection(TransactionType::TRANSACTION, $data['quantity']);
            $amount                 = $data['amount'];
            $description            = $data['description'] ?? null;
            $paymentMethod          = $data['payment_method'];
            $paidAmount             = $data['paid_amount'];

            # Create New Record Stock
            $newStock = $this->stockItemService->autoStockFromTransaction(
                $itemId,
                $salesQuantity,
                $amount,
                $cogs
            );

            # get new stock id 
            $newStockId     = $newStock->id;

            Log::debug('TransactionService:stock created from transaction', [
                'stock_id' => $newStockId,
                'qty_stock' => $salesQuantity
            ]);

            # Generate Transaction Number
            $trxNumber = $this->generateTrxNumber($itemId);

            /**
             * Transform and load payload transaction
             */
            $dataTransaction = [
                'item_id'       => $itemId,
                'customer_id'   => $customerId,
                'trx_number'    => $trxNumber,
                'stock_id'      => $newStockId,
                'quantity'      => $transactionQuantity,
                'amount'        => $amount,
                'description'   => $description,
            ];

            # create transaction
            $transaction = $this->repository->create($dataTransaction);

            Log::info('TransactionService:transaction created', [
                'transaction_id'    => $transaction->id,
                'trx_number'        => $trxNumber
            ]);

            /**
             * Transform data receivable
             */

            $dataReceivable = [
                'customer_id'       => $customerId,
                'item_id'           => $itemId,
                'quantity'          => $transactionQuantity,
                'price'             => $amount,
                'payment_method'    => $paymentMethod,
                'paid_amount'       => $paidAmount,
                'description'       => $description,
            ];

            # Auto create receivable from transaction
            $this->receivableService->autoCreateReceivableFromTransaction(
                $transaction,
                $dataReceivable
            );

            Log::info('TransactionService:receivable created from transaction', [
                'transaction_id' => $transaction->id
            ]);

            return $transaction->fresh([
                'customer',
                'masterItem',
                'receivables.receivableItems',
                'receivables.receivablePayment'
            ]);

        });

    } catch (\Throwable $e) {

        Log::error('TransactionService:createTransaction failed', [
            'error' => $e->getMessage(),
            'payload' => $data
        ]);

        throw $e;
    }
}

    /**
     * Update transaction & dependant stock
     *
     * update transaction cannot stand alone
     * because transaction is depend on stock.
     * Mirrors the same atomic flow as createTransaction.
     */
    public function autoUpdateTransaction(int $id, array $data)
    {
        Log::info('TransactionService:updateTransaction started', [
            'transaction_id' => $id,
            'item_id'        => $data['item_id'],
            'customer_id'    => $data['customer_id'],
            'quantity'       => $data['quantity'],
        ]);

        try {

            return DB::transaction(function () use ($id, $data) {

                # Get and validate existence data
                $transaction = $this->getTransactionById($id);
                $customer    = $this->customerService->getCustomerById($data['customer_id']);
                $masterItem  = $this->masterItemService->findById($data['item_id']);
                $stock       = $this->stockItemService->findById($transaction->stock_id);

                Log::debug('TransactionService:updateTransaction master data loaded', [
                    'transaction_id' => $transaction->id,
                    'master_item_id' => $masterItem->id,
                    'customer_id'    => $customer->id,
                    'stock_id'       => $stock->id,
                ]);
                /**
                 * Transform data stock
                 * - Sales → stock decrease (minus)
                 */
                $itemId                 = $masterItem->id;
                $customerId             = $customer->id;
                $stockId                = $stock->id;
                $salesQuantity          = $this->resolveStockDirection(TransactionType::SALES, $data['quantity']);
                $transactionQuantity    = $this->resolveStockDirection(TransactionType::TRANSACTION, $data['quantity']);
                $amount                 = $data['amount'];
                $description            = $data['description'] ?? null;

                $paymentMethod          = $data['payment_method'];
                $paidAmount             = $data['paid_amount'];

                /**
                 * Prepare transaction payload
                 * - quantity stored as positive (raw value from request)
                 * - only the stock record uses the signed/negative direction
                 */
                $dataTrx = [
                    'item_id'     => $itemId,
                    'customer_id' => $customerId,
                    'quantity'    => $transactionQuantity,
                    'amount'      => $amount,
                    'description' => $description,
                    'stock_id'    => $stockId,
                ];

                # Update transaction record
                $this->repository->update($transaction, $dataTrx);

                Log::debug('TransactionService:updateTransaction transaction record updated', [
                    'transaction_id' => $transaction->id,
                ]);

                /**
                 * Update stock
                 * - Sales direction → stock decreases (negative)
                 */
                $newStock = [
                    'item_id' => $itemId,
                    'stock'   => $salesQuantity,
                ];

                $this->stockItemService->updateStock($stockId, $newStock);

                Log::debug('TransactionService:updateTransaction stock updated', [
                    'stock_id'  => $stockId,
                    'new_stock' => $newStock['stock'],
                ]);

                /**
                 * Prepare receivable payload
                 * - key 'price' matches autoUpdateReceivableFromTransaction expectation
                 */
                $dataReceivable = [
                    'customer_id'    => $customer->id,
                    'item_id'        => $masterItem->id,
                    'quantity'       => $transactionQuantity,
                    'price'          => $amount,
                    'payment_method' => $paymentMethod,
                    'paid_amount'    => $paidAmount,
                    'description'    => $description,
                ];

                # Auto-update receivable from transaction
                $this->receivableService->autoUpdateReceivableFromTransaction($id, $dataReceivable);

                Log::info('TransactionService:updateTransaction receivable updated', [
                    'transaction_id' => $transaction->id,
                ]);

                /**
                 * Return fresh transaction with all relations reloaded,
                 * identical shape to createTransaction so TransactionResource works correctly.
                 */
                return $transaction->fresh([
                    'customer',
                    'masterItem',
                    'receivables.receivableItems',
                    'receivables.receivablePayment',
                ]);
            });

        } catch (\Throwable $e) {

            Log::error('TransactionService:updateTransaction failed', [
                'transaction_id' => $id,
                'error'          => $e->getMessage(),
                'payload'        => $data,
            ]);

            throw $e;
        }
    }

    /**
     * Get transaction by ID
     */
    public function getTransactionById(int $id)
    {
        $transaction = $this->repository->findById($id);

        if (! $transaction) {
            throw new HttpResponseException(response()->json([
                'error' => 'NOT_FOUND',
            ], 404));
        }

        return $transaction;
    }

    /**
     * Get transaction by specific date
     */
    public function getTransactionByDate($date)
    {
        $transaction = $this->repository->getTransactionByDate($date);

        if (! $transaction) {
            throw new HttpResponseException(response()->json([
                'error' => 'NOT_FOUND',
            ], 404));
        }

        return $transaction;
    }

    /**
     * Get daily sales aggregation (per month)
     */
    public function getDailySale()
    {
        $transaction = $this->repository->getDailySalePerMonth();

        if (! $transaction) {
            throw new HttpResponseException(response()->json([
                'error' => 'DAILY_SALE_NOT_FOUND',
            ], 404));
        }

        return $transaction;
    }

    /**
     * Get top 10 customers by transaction value
     */
    public function getTopCustomer()
    {
        $transaction = $this->repository->getTop10Customer();

        if (! $transaction) {
            throw new HttpResponseException(response()->json([
                'error' => 'TOP_CUSTOMER_NOT_FOUND',
            ], 404));
        }

        return $transaction;
    }

    /**
     * Get outstanding transactions
     */
    public function getOutsTransaction()
    {
        $transaction = $this->repository->getOutstandingTransaction();

        if (! $transaction) {
            throw new HttpResponseException(response()->json([
                'error' => 'OUTSTANDING_TRX_NOT_FOUND',
            ], 404));
        }

        return $transaction;
    }

    /**
     * Generate unique transaction number
     *
     * Format:
     * TRX-YYYYMMDD-ITEMID-XXXX
     */
    public function generateTrxNumber($itemId)
    {
        return DB::transaction(function () use ($itemId) {

            $date = Carbon::now()->format('Ymd');

            /**
             * Ambil transaksi terakhir berdasarkan item
             * Digunakan untuk generate sequence berikutnya
             */
            $lastTrx = $this->repository->findLastTransaction($itemId);

            if ($lastTrx && preg_match('/(\d{4})$/', $lastTrx->trx_number, $matches)) {
                $seq = (int) $matches[1] + 1;
            } else {
                $seq = 1;
            }

            // Pastikan sequence selalu 4 digit
            $seqPadded = str_pad($seq, 4, '0', STR_PAD_LEFT);

            $trxNumber = "TRX-{$date}-{$itemId}-{$seqPadded}";

            if (! $trxNumber) {
                throw new HttpResponseException(response()->json([
                    'TRX_NUMBER_FAIL_GENERATE',
                ], 400));
            }

            return $trxNumber;
        });
    }

    private function resolveStockDirection(TransactionType $type, int $quantity): int
    {
        return match($type) {
            TransactionType::SALES       => -$quantity,  // stok berkurang
            TransactionType::TRANSACTION => +$quantity,  // stok bertambah
            TransactionType::RETURN      => +$quantity,  // stok bertambah
            TransactionType::ADJUST      => +$quantity,  // stok bertambah
        };
    }
}
