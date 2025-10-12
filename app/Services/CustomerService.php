<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;
use League\Csv\Reader;
use DB;

class CustomerService
{
    protected $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data, $user)
    {
        $this->validateCustomerName($data['customer_name']);
        $this->validateCustomerEmail($data['email']);

        if(!isset($data['nik']) || $data['nik'] == 0) {
            $data['nik'] = $this->repository->generateRandomNumber();
        }

        $this->validateCustomerNik($data['nik']);

        $newData = array_merge($data, [
            'created_by' => $user->id,
        ]);

        $customer = $this->repository->create($newData);
        return $customer;
    }

    public function update($id, $data, $user)
    {
        $customer = $this->getCustomerById($id);

        if($data['customer_name'] != $customer->customer_name)
        {
            $this->validateCustomerName($data['customer_name']);
        }

        if($data['email'] != $customer->email)
        {
            $this->validateCustomerEmail($data['email']);
        }

        if($data['nik'] != $customer->nik)
        {
            $this->validateCustomerNik($data['nik']);
        }

        $newData = array_merge($data, [
            'updated_by' => $user->id,
        ]);

        return $this->repository->update($customer,$newData);
        
    }

    public function getCustomerById($id)
    {
        $customer = $this->repository->findById($id);
        if(!$customer){
            throw new HttpResponseException(response()->json([
                'error' => 'CUSTOMER_NOT_FOUND' 
            ])->setStatusCode(404));
        }

        return $customer;
    }

    public function getAllCustomer()
    {
        $customer = $this->repository->getAll();
        if(!$customer) {
            throw new HttpResponseException(response()->json([
                'error' => 'CUSTOMER_NOT_FOUND'
            ])->setStatusCode(404));
        }

        return $customer;
    }

    public function searchCustomer($request)
    {
        $customer = $this->repository->search($request);

        if(!$customer) {
            throw new HttpResponseException(response()->json( [
                'error' => 'CUSTOMER_PAGE_NOT_FOUND'
            ])->setStatusCode(404));
        }

        return $customer;
    }

    public function inactiveCustomer($id, $user)
    {
        $customer = $this->getCustomerById($id);

        if($customer->active_flag == 'Y') {
            $customer->active_flag = 'N';
            $customer->inactive_date = Carbon::now();
        } else {
            $customer->active_flag = 'Y';
            $customer->inactive_date = NULL;
        }

         $customer->updated_by = $user->id;
         $customer->save();
         return $customer;
    }

    public function importCsv($data)
    {
        $path = $data->getRealPath();

        try {
            $csv = Reader::createFromPath($path, 'r');
            $csv->setHeaderOffset(0); //first rows is always header

            $records = $csv->getRecords();
            $successInsert = 0;

            DB::beginTransaction();
            foreach($records as $record) {
                $this->repository->insert($record);
                $successInsert++;
            }
            
            DB::commit();
            return $successInsert;
            
        } catch (\Exception $error) {
            
            DB::rollBack();
            throw $error;
            
        }
    }

    public function validateCustomerName($customerName)
    {
        $customer = $this->repository->validateName($customerName);

        if($customer) {
            throw new HttpResponseException(response()->json([
                'error' => 'CUSTOMER_NAME_EXISTS'
            ])->setStatusCode(400));
        }
    }

    public function validateCustomerEmail($email)
    {
        $customer = $this->repository->validateEmail($email);
        if($customer) {
            throw new HttpResponseException(response()->json([
                'error' => 'CUSTOMER_EMAIL_EXISTS'
            ])->setStatusCode(400));
        }
    }

    public function validateCustomerNik($nik)
    {
        $customer = $this->repository->validateNik($nik);
        if($customer) {
            throw new HttpResponseException(response()->json([
                'error' => 'CUSTOMER_NIK_EXISTS'
            ])->setStatusCode(400));
        }
    }
}