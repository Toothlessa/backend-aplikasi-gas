<?php

namespace App\Http\Controllers;

use App\Http\Requests\Debt\DebtCreateRequest;
use App\Http\Requests\Debt\DebtUpdateRequest;
use App\Http\Resources\Debt\DebtCollection;
use App\Http\Resources\Debt\DebtCreateResource;
use App\Http\Resources\Debt\DebtSummaryCollection;
use App\Http\Resources\Debt\DebtUpdateResource;
use App\Services\DebtService;
use Illuminate\Support\Facades\Auth;

class DebtController extends Controller
{
    
    protected $service;

    public function __construct(DebtService $service)
    {
        $this->service = $service;
    }

    public function create(DebtCreateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $debt = $this->service->create($data, $user);

        return (new DebtCreateResource($debt))->response()->setStatusCode(201);
    }

    public function update($id, DebtUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $debt = $this->service->update($id, $data, $user);

        return (new DebtUpdateResource($debt))->response()->setStatusCode(200);
    }

    public function findById($id) 
    {
        Auth::user();
        $debt = $this->service->findById($id);

        return (new DebtCreateResource($debt))->response()->setStatusCode(200);
    }

    public function findAll()
    {
        Auth::user();
        $debt = $this->service->findAll();

        return(new DebtCollection($debt)->response()->setStatusCode(200));
    }

    public function findDebtByCustId($customerId)
    {
        Auth::user();
        $debt = $this->service->findDebtByCustId($customerId);

        return (new DebtCollection($debt)->response()->setStatusCode(200));
    }

    public function findDebtSummary(): DebtSummaryCollection {
        Auth::user();

        $debt = $this->service->findSummaryDebtGroupByCustomer();

        return new DebtSummaryCollection($debt);
    }

    public function findDebtOutstanding(): DebtSummaryCollection {
        
        Auth::user();
        $debt = $this->service->findDebtOutstanding();

        return new DebtSummaryCollection($debt);
    }
}
