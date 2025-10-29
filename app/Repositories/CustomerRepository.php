<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomerRepository
{
    public function create($data)
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data)
    {
        $customer->fill($data);
        $customer->save();
        return $customer;
    }

    public function insert(array $data): bool
    {
        return Customer::insert($data);
    }

    public function findById($id)
    {
        return Customer::find($id);
    }

    public function getAll()
    {
        return Customer::query()->orderBy('active_flag')
                                   ->orderBy('customer_name')
                                   ->get();

    }

    public function search($request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 10);

        $customer = Customer::query();

        $customer = $customer->where(function (Builder $builder) use ($request)
        {
            $customerName = $request->input('customer_name');
            if($customerName) {
                $builder->where(function(Builder $builder) use ($customerName) 
                {
                    $builder->orWhere('customer_name', 'like', '%'.$customerName.'%');
                });
            }

            $email = $request->input('email');
            if($email) {
                $builder->where('email', 'like', '%'.$email.'%');
            }

            $nik = $request->input('nik');
            if($nik) {
                $builder->where('nik', 'like', '%'.$nik.'%');
            }

            $address = $request->input('address');
            if($address) {
                $builder->where('address', 'like', '%'.$address.'%');
            }

            $phone = $request->input('phone');
            if($phone) {
                $builder->where('phone', 'like', '%'.$phone.'%');
            }
        });

        return $customer->paginate(perPage: $size, page: $page);
    }

    public function validateName($customerName)
    {
        return Customer::where('customer_name', $customerName)->exists();
    }

    public function validateEmail($email)
    {
        return Customer::where('email', $email)->exists();
    }

    public function validateNik($nik)
    {
        return Customer::where('nik', $nik)->exists();
    }

    public function generateRandomNumber()
    {
        return mt_rand(100,999);
    }
}