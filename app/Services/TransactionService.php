<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

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
    public function createTransaction($data)
    {
        return DB::transaction(function () use ($data) {

            # validate data existence
            $masterItem     = $this->masterItemService->findById($data['item_id']);
            $customer       = $this->customerService->getCustomerById($data['customer_id']);

            /**
             * Transform data stock
             * - Sales → stock decrease (minus)
             */
            $qtyStock           = -$data['quantity'];
            $sellingPrice       = $data['amount'];

            # Create New Record Stock
            $newStock = $this->stockItemService->autoStockFromTransaction(
                $masterItem->id,
                $qtyStock,
                $sellingPrice,
                $masterItem->cost_of_goods_sold
            );

            # Generate Transaction Number
            $trx_number = $this->generateTrxNumber($masterItem->id);

            /**
             * Transform and load payload transaction
             * all calculation is done in service and models
             * not in controller or repository
             */
            $dataTransaction = [
                'item_id'       => $masterItem->id,
                'customer_id'   => $customer->id,
                'trx_number'    => $trx_number,
                'stock_id'      => $newStock->id,
                'quantity'      => $data['quantity'],
                'amount'        => $data['amount'],
                'description'   => $data['description'],
            ];

            # create transaction
            $transaction = $this->repository->create($dataTransaction);

            /**
             * Transform data receivable
             * - Auto create data receivable from transaction
             */

            # Prepare receivable data
            $dataReceivable = [
                'customer_id'       => $customer->id,
                'item_id'           => $masterItem->id,
                'quantity'          => $data['quantity'],
                'price'             => $data['amount'],
                'payment_method'    => $data['payment_method'],
                'paid_amount'       => $data['paid_amount'],
                'description'       => $data['description'],
                # source and source_id handled in morph models
            ];

            # Auto create receivable from transaction
            $this->receivableService->autoCreateReceivableFromTransaction($transaction, $dataReceivable);

            // return [
            //     'customer'              => $customer,
            //     'master_item'           => $masterItem,
            //     'transaction'           => $transaction,
            //     'receivable_payment'    => $receivable->receivablePayment->first(),
            // ];
            return $transaction->load([
                'customer',
                'masterItem',
                'receivables.receivablePayment'
            ]);

        });
    }

    /**
     * Update transaction & dependant stock
     *
     * update transaction cannot stand alone
     * because transaction is depend on stock
     */
//     public function updateTransaction(int $id, array $data)
//     {
//         return DB::transaction(function () use ($id, $data) {

//             # get and validate existence data
//             $transaction = $this->getTransactionById($id);
//             $customer    = $this->customerService->getCustomerById($data['customer_id']);
//             $masterItem  = $this->masterItemService->findById($data['item_id']);
//             $stock       = $this->stockItemService->findById($transaction->stock_id);
//             /**
//              * Re-calculate data transaction
//              * Prepare transaction data
//              */
//             $dataTrx = [
//                 'item_id'       => $masterItem->id,
//                 'customer_id'   => $customer->id,
//                 'quantity'      => $data['quantity'],
//                 'amount'        => $data['amount'],
//                 'description'   => $data['description'],
//                 'stock_id'      => $stock->id,
//             ];

//             # update transaction
//             $this->repository->update($transaction, $dataTrx);

//             /**
//              * update stock
//              * -change of stock based on transaction update
//              */
//             $newStock = [
//                 'item_id' => $masterItem->id,
//                 'stock' => -$data['quantity'],
//             ];
//             # update stock
//             $this->stockItemService->updateStock($stock->id, $newStock);

//               /**
//              * Transform data receivable
//              * - Auto create data receivable from transaction
//              */

//             # Prepare receivable data
//             $dataReceivable = [
//                 'transaction_id'    => $transaction->id,
//                 'customer_id'       => $customer->id,
//                 'item_id'           => $masterItem->id,
//                 'quantity'          => $data['quantity'],
//                 'price'             => $data['amount'],
//                 'payment_method'    => $data['payment_method'],
//                 'paid_amount'       => $data['paid_amount'],
//                 'description'       => $data['description'],
//             ];
// `
//             # Prepare receivable data`

//             # Auto create receivable from transaction
//             $receivable = $this->receivableService->autoUpdateReceivableFromTransaction($dataReceivable);

//             // return $transaction;
//             return [
//                 'customer'              => $customer,
//                 'master_item'           => $masterItem,
//                 'transaction'           => $transaction,
//                 'receivable_payment'    => $receivable->receivablePayment->first(),
//             ];

//             /**
//              * Refresh used to:
//              * - latest data from DB
//              * - relations are reloaded
//              */
//             // return $transaction->refresh();
//         });
//     }

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
}
