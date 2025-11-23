<?php

namespace App\Repositories;

use App\Models\Debt;

class DebtRepository
{
    public function create($data) 
    {
        return Debt::create($data);
    }

    public function update(Debt $debt, $data)
    {
        $debt->fill($data);
        $debt->save();

        return $debt;
    }

    public function findAll()
    {
        return Debt::all();
    }

    public function findById($id)
    {
        return Debt::find($id);
    }

    public function findDebtByCustId($customerId)
    {
        return Debt::with('customer')
                    ->where('customer_id', $customerId)
                    ->orderBy('created_at')
                    ->get();
    }

     public function findSummaryDebtGroupByCustomer() 
     {

        $debt = Debt::selectRaw('customer_id, SUM(amount_pay) as total_pay, SUM(total) as total_debt, SUM(total) - SUM(amount_pay) AS debt_left')
                ->groupBy('customer_id')
                ->with('customer:id,customer_name')
                ->get();

        return $debt;
    } 

    public function findDebtOutstanding() {

        $debt = Debt::selectRaw('customer_id,
                                 SUM(amount_pay) AS total_pay,
                                 SUM(total) AS total_debt,
                                 SUM(amount_pay) - SUM(total) AS debt_left')
                    ->groupBy('customer_id')
                    ->havingRaw("SUM(COALESCE(amount_pay, 0)) - SUM(total) != 0")
                    ->with('customer:id,customer_name')
                    ->get();

        return $debt;
    }
}