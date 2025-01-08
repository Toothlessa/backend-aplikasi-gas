<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function getCustomer(int $id): Customer
    {
        $customer = Customer::where('id', $id)->first();
        if(!$customer){
            throw new HttpResponseException(response()->json([
                'errors' => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $customer;
    }

    public function checkCustomerExists(string $customerName)
    {
        if(Customer::where("customer_name", $customerName)->count()==1){
            throw new HttpResponseException(response([
                "errors"=> [
                    "customer_name" => [
                        "Customer dengan nama tersebut sudah terdaftar"
                    ]
                ]
            ], 400));
        }
    }

    public function checkEmailExists(string $email)
    {
        if(Customer::where("email", $email)->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "email" => [
                        "Email sudah terdaftar"
                    ]
                ]
            ], 400));
        }
    }

    public function create(CustomerCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        
        $this->checkCustomerExists($data["customer_name"]);
        $this->checkEmailExists($data["email"]);

        $customer = new Customer($data);
        $customer->created_by = $user->id;
        $customer->save();

        return (new CustomerResource($customer))->response()->setStatusCode(201);
    }

    public function get($id): CustomerResource
    {
        $user = Auth::user();
        $customer = $this->getCustomer($id);

        return new CustomerResource($customer);
    }


    public function update($id, CustomerUpdateRequest $request): CustomerResource
    {
        $user = Auth::user();
        $customer = $this->getCustomer($id);
        $data = $request->validated();

        $customer->fill($data);

        $this->checkCustomerExists($data["customer_name"]);
        $this->checkEmailExists($data["email"]);

        $customer->updated_by = $user->id;
        $customer->save();

        return new CustomerResource($customer);
    }

    public function delete($id): JsonResponse
    {
        $user = Auth::user();
        $customer = $this->getCustomer($id);
        $customer->delete();

        return response()->json([
            'data' => true
        ])->setStatusCode(200);
    }

    public function search(Request $request): CustomerCollection
    {
        $user = Auth::user();
        $page = $request->input('page',1);
        $size = $request->input('size',10);
        
        $customer = Customer::query();

        $customer = $customer->where(function (Builder $builder) use ($request){
            $customerName = $request->input('customer_name');
            if($customerName){
              $builder->where(function(Builder $builder) use ($customerName){
                $builder->orWhere('customer_name','like','%'.$customerName.'%');
                });  
            }

            $nik = $request->input('nik');
            if($nik){
                $builder->where('nik','like','%'.$nik.'%');
            }

            $email = $request->input('email');
            if($email){
                $builder->where('email','like','%'.$email.'%');
            }

            $address = $request->input('address');
            if($address){
                $builder->where('address','like','%'.$address.'%');
            }

            $phone = $request->input('phone');
            if($phone){
                $builder->where('phone','like','%'.$phone.'%');
            }
        });

        $customer = $customer->paginate(perPage: $size, page: $page);

        return new CustomerCollection($customer);
    }
}
