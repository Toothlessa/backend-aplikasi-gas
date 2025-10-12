<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\CustomerCreateRequest;
use App\Http\Requests\Customer\CustomerImportCsvRequest;
use App\Http\Requests\Customer\CustomerUpdateRequest;
use App\Http\Resources\Customer\CustomerCollection;
use App\Http\Resources\Customer\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }    

    public function create(CustomerCreateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $customer = $this->service->create($data, $user);

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    public function update($id, CustomerUpdateRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        $customer = $this->service->update($id, $data, $user);
        return new CustomerResource($customer);
    }

    public function get($id)
    {
        Auth::user();

        $customer = $this->service->getCustomerById($id);

        return (new CustomerResource($customer))->response()->setStatusCode(200);
    }

    public function getAll(): CustomerCollection
    {
        $user = Auth::user();
        $customer = $this->service->getAllCustomer();

        return new CustomerCollection($customer);
    }

    public function search(Request $request)
    {
        Auth::user();
        $customer = $this->service->searchCustomer($request);

        return new CustomerCollection($customer);
    }

    public function inactiveCustomer($id)
    {
        $user = Auth::user();
        
        $customer = $this->service->inactiveCustomer($id,$user);

        return new CustomerResource($customer);
    }

    public function importCsv(CustomerImportCsvRequest $request)
    {
        $data = $request->file('csvFile');

        try{
            $csvImport = $this->service->importCsv($data);

            return response()->json([
                'message' => 'CSV imported successfully',
                'total_success' => $csvImport
            ]);
        } catch (\Exception $error) {
            return response()->json(['message' => 'Error importing CSV: ' . $error->getMessage()], 500);
        }
    }

}
