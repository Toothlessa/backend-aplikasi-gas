<?php

namespace App\Http\Controllers;

use App\Http\Requests\DebtCreateRequest;
use App\Http\Resources\DebtCollection;
use App\Http\Resources\DebtCreateResource;
use App\Http\Resources\DebtGetSummaryCollection;
use App\Http\Resources\DebtGetSummaryResource;
use App\Models\Debt;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class DebtController extends Controller
{

    public function queryGetDebtSum() {

        $debt = DB::table("debts")
            ->join("customers", "customer_id", 'customers.id')
            ->selectRaw("customer_id, customer_name, SUM(amount_pay) as total_pay, SUM(total) as total_debt,
                    SUM(amount_pay) - SUM(total) as  debt_left")
            ->groupBy("customer_name")
            ->orderByRaw("SUM(amount_pay) - SUM(total), debts.created_at")
            ->get();

        if(!$debt){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $debt;
    } 

    public function queryGetDebtOutstanding() {
        $debt = DB::table("debts")
            ->join("customers", "customer_id", 'customers.id')
            ->selectRaw("customer_id, customer_name, SUM(amount_pay) as total_pay, SUM(total) as total_debt,
                    SUM(amount_pay) - SUM(total) as  debt_left")
            ->groupBy("customer_name")
            ->havingRaw("SUM(COALESCE(amount_pay, 0)) - SUM(total) != 0")
            ->get();

        if(!$debt){
            throw new HttpResponseException(response()->json([
                "errors" => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return $debt;
    }

    public function create(DebtCreateRequest $request, $customerId): JsonResponse
    {
        $user = FacadesAuth::user();      
        $data = $request->validated();  
        $debt = new Debt($data);

        if(($debt["amount_pay"] === 0 && $debt["total"] === 0)) {
            throw new HttpResponseException(response()->json(data: [
                'errors' => "One of the amount field must be filled"
            ])->setStatusCode(code: 400));
        }

        $debt->customer_id = $customerId;
        $debt->created_by = $user->id;
        $debt->save();

        $summaryDebt = $this->getDebtSummary();

        return (new DebtGetSummaryCollection($summaryDebt))->response()->setStatusCode(201);
    }

    public function get(): DebtCollection {
        $user = FacadesAuth::user();
        $debt = Debt::all();

        return new DebtCollection($debt);
    }

    public function getById($id): Debt {
        $user = FacadesAuth::user();
        $debt = Debt::find($id);

        return $debt;
    }

    public function getByCust(int $customerId): DebtCollection
    {
        $user = FacadesAuth::user();

        $debt = Debt::where('customer_id', $customerId)
                        ->orderBy("created_at")
                        ->get();

        if(!$debt){
            throw new HttpResponseException(response()->json(data: [
                'errors' => "NOT_FOUND"
            ])->setStatusCode(404));
        }

        return new DebtCollection($debt);
    }

    public function getDebtSummary(): DebtGetSummaryCollection {
        $user = FacadesAuth::user();
        $debt = $this->queryGetDebtSum();

        return new DebtGetSummaryCollection($debt);
    }

    public function getDebtOutstanding(): DebtGetSummaryCollection {
        $user = FacadesAuth::user();
        $debt = $this->queryGetDebtOutstanding();

        return new DebtGetSummaryCollection($debt);
    }

    public function update($id, DebtCreateRequest $request): DebtCreateResource
    {
        $user = FacadesAuth::user();
        $debt = $this->getById($id);
        $data = $request->validated();

        $debt->fill($data);

        $debt->updated_by = $user->id;
        $debt->save();

        return new DebtCreateResource($debt);
    }
}
