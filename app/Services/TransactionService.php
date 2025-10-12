<?php

namespace App\Services;

use App\Repositories\StockItemRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\MasterItemRepository;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $repository;
    protected $masterItemRepository;
    protected $stockItemRepository;

     public function __construct( TransactionRepository $repository,
                                  MasterItemRepository $masterItemRepository,
                                  StockItemRepository $stockItemRepository) 
    {
        $this->repository = $repository;
        $this->masterItemRepository  = $masterItemRepository;
        $this->stockItemRepository = $stockItemRepository;
    }

    public function create($dataStock, $dataTrx, $user)
    {

        return DB::transaction(function () use ($dataStock, $dataTrx, $user) {
            // Get item
            $masterItem = $this->masterItemRepository->findById($dataTrx['item_id']);

            // Get the latest stock item
            $stockItem = $this->stockItemRepository->findLatestStock($dataTrx['item_id']);

            // 3. Prepare stock item data
            $stockData = array_merge($dataStock, [
                'item_id'      => $masterItem->id,
                'cogs'         => $masterItem->cost_of_goods_sold,
                'selling_price'=> $masterItem->selling_price,
                'prev_stock_id'=> $stockItem ? $stockItem->id : 0,
                'created_by'   => $user->id,
            ]);

            $newStockItem = $this->stockItemRepository->create($stockData);

            $trx_number = $this->generateTrxNumber($masterItem->id);
            // Create the transaction
           $trxData = array_merge($dataTrx, [
                'item_id'     => $masterItem->id,
                'trx_number'  => $trx_number,
                'customer_id' => $dataTrx['customer_id'],
                'stock_id'    => $newStockItem->id,
                'created_by'  => $user->id,
            ]);

            $newTrxData = $this->repository->create($trxData);

            return $newTrxData;
        });
    }

    public function update($id, array $dataTrx, array $dataStock, $user)
    {
        return DB::transaction(function () use ($id, $dataTrx, $dataStock, $user) {
            // update transaction
            $transaction = $this->repository->findById($id);
            $dataTrx['updated_by'] = $user->id;
            $transaction = $this->repository->update($transaction, $dataTrx);

            // update stock
            $stock = $this->stockItemRepository->findById($dataStock['stock_id']);
            $dataStock['updated_by'] = $user->id;
            $this->stockItemRepository->update($stock, $dataStock);

            return $transaction;
        });
    }


    public function getTransactionById(int $id)
    {
        $transaction = $this->repository->findById($id);

        if (!$transaction) {
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ], 404));
        }

        return $transaction;
    }
    
    public function getTransactionByDate($date)
    {
        $transaction = $this->repository->getTransactionByDate($date);

        if(!$transaction){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getDailySale()
    {
        $transaction = $this->repository->getDailySalePerMonth();

        if(!$transaction) {
            throw new HttpResponseException(response()->json([
                "errors" => "DAILY_SALE_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getTopCustomer()
    {
        $transaction = $this->repository->getTop10Customer();

        if(!$transaction) {
            throw new HttpResponseException(response()->json([
                "errors" => "TOP_CUSTOMER_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function getOutsTransaction()
    {
        $transaction = $this->repository->getOutstandingTransaction();

        if(!$transaction) {
            throw new HttpResponseException(response()->json([
                "errors" => "OUTSTANDING_TRX_NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $transaction;
    }

    public function generateTrxNumber($itemId)
    {
        return DB::transaction(function () use ($itemId) {
            $date = Carbon::now()->format('Ymd');

            // Kunci transaksi terakhir untuk item_id tertentu agar tidak dibaca bersamaan
            $lastTrx = $this->repository->findLastTransaction($itemId);

            if ($lastTrx && preg_match('/(\d{4})$/', $lastTrx->trx_number, $matches)) {
                $seq = (int)$matches[1] + 1;
            } else {
                $seq = 1;
            }

            $seqPadded = str_pad($seq, 4, '0', STR_PAD_LEFT);

            $trxNumber = "TRX-{$date}-{$itemId}-{$seqPadded}";

            if(!$trxNumber) {
                throw new HttpResponseException(response()->json([
                    'TRX_NUMBER_FAIL_GENERATE',
                ])->setStatusCode(400));
            }

            return $trxNumber;
        });
    }

}
