<?php

namespace App\Services;

use App\Repositories\DebtRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class DebtService
{
    protected $repository;

    public function __construct(DebtRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data, $user)
    {
        $this->validateOneAmountNotZero($data['amount_pay'], $data['total']);

        $data = array_merge($data, [
            'created_by' => $user->id, 
        ]);

        return $this->repository->create($data);
    }

    public function update($id, $data, $user)
    {
        $debt = $this->findById($id);

        $data = array_merge($data, [
            'updated_by' => $user->id,
        ]);

        return $this->repository->update($debt, $data);
    }

    public function findAll()
    {
        $debt = $this->repository->findAll();

        if(!$debt) {
            throw new HttpResponseException(response()->json([
                'errors' => 'NO_DEBT_FOUND',
            ])->setStatusCode(404));
        }

        return $debt;
    }

    public function findById($id)
    {
        $debt = $this->repository->findById($id);

        if(!$debt) {
            throw new HttpResponseException(response()->json([
                'errors' => 'DEBT_NOT_FOUND'
            ])->setStatusCode(404));
        }

        return $debt;
    }

    public function findDebtByCustId($customerId)
    {
        $debt = $this->repository->findDebtByCustId($customerId);

        if(!$debt) {
            throw new HttpResponseException(response()->json([
                'errors' => 'CUSTOMER_WITH_DEBT_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $debt;
    }

    public function findSummaryDebtGroupByCustomer()
    {
        $debt = $this->repository->findSummaryDebtGroupByCustomer();

        if(!$debt) {
            throw new HttpResponseException(response()->json([
                'errors' => 'SUMMARY_DEBT_GROUP_CUSTOMER_NAME_NOT_FOUND',
            ])->setStatusCode(404));
        }
        
        return $debt;
    }

    public function findDebtOutstanding()
    {
        $debt = $this->repository->findDebtOutstanding();

        if(!$debt) {
            throw new HttpResponseException(response()->json([
                'errors' => 'DEBT_OUTSTANDING_NOT_FOUND',
            ])->setStatusCode(404));
        }

        return $debt;
    }   

    public function validateOneAmountNotZero($amount0, $amount1)
    {
        if($amount0 == 0 && $amount1 == 0) {
            throw new HttpResponseException(response()->json([
                'errors' => 'ONE_OF_AMOUNT_MUST_NOT_ZERO'
            ])->setStatusCode(400));
        }
    }
}