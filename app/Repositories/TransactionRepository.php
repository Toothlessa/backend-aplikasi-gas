<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionRepository
{

    public function create($data)
    {
        return Transaction::create($data);
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        $transaction->fill($data);
        $transaction->save();
        return $transaction;
    }
    
    public function findById(int $id): ?Transaction
    {
        return Transaction::find($id);
    }

    public function getTransactionByDate($date)
    {

    return Transaction::with(['customer', 'stockItem'])
        ->whereDate('created_at', $date)
        ->orderByDesc('created_at')
        ->get([
            'id',
            'customer_id',
            'stock_id',
            'quantity',
            'amount',
            'total',
            'description',
            'item_id',
            'created_by',
            'created_at',
        ]);
    }

    public function getDailySalePerMonth()
    {
        return Transaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS month,
                                     DATE_FORMAT(created_at, '%d') AS day,  
                                     sum(quantity) as total")
                ->where("created_at", ">=", Carbon::now()->subDays(30))
                ->orderByDesc("created_at")
                ->groupBy("day")
                ->groupBy("month")
                ->limit(30)
                ->get();

    }

    public function getTop10Customer()
    {
        return Transaction::query()
            ->selectRaw('customer_id, customers.customer_name, SUM(quantity) as total')
            ->join('customers', 'customers.id', '=', 'transactions.customer_id')
            ->whereNot(function ($query) {
                $query->where('customers.customer_name', 'like', 'umum')
                    ->orWhere('customers.customer_name', 'like', '%aulia%fauziah%')
                    ->orWhere('customers.customer_name', 'like', 'balancing')
                    ->orWhere('customers.customer_name', 'like', 'ust%mustofa%');
            })
            ->groupBy('customer_id', 'customers.customer_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();
    }

    public function getOutstandingTransaction()
    {
        return Transaction::with(['customer', 'masterItem'])
            ->whereNot(function ($query) {
                $query->whereIn('description', ['umum', 'balancing'])
                    ->orWhere('description', 'like', '%done%')
                    ->orWhere('description', 'like', '%teh iya%');
            })
            ->orderBy('customer_id')
            ->orderBy('created_at')
            ->get([
                'id', 
                'stock_id', 
                'customer_id', 
                'item_id', 
                'description', 
                'quantity', 
                'amount', 
                'total', 
                'created_at'
            ]);
    }

    public function findLastTransaction($itemId)
    {
        return DB::table('transactions')
                ->where('item_id', $itemId)
                ->whereDate('created_at', Carbon::today())
                ->lockForUpdate() // 🔒 ini penting — mengunci baris sampai transaksi selesai
                ->orderByDesc('id')
                ->first();
    }

}
