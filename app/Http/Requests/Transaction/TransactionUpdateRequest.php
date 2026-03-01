<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionUpdateRequest extends FormRequest
{
   
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    public function rules(): array
    {
        // return [
        //     'customer_id'   => ['required', 'numeric'],
        //     'item_id'       => ['required', 'numeric'],
        //     'quantity'      => ['required', 'numeric', 'min:1'],
        //     'description'   => ['max:100'],
        //     'amount'        => ['required', 'numeric', 'min:0'],
        // ];
        return [
            # customer
            'customer_id'   => ['required', 'numeric'],
            # item
            'item_id'       => ['required', 'numeric'],
            # transaction
            'quantity'      => ['required', 'numeric', 'min:1'],
            'description'   => ['nullable', 'max:100'],
            'amount'        => ['required', 'numeric', 'min:0'],
            # payment
            'payment_method'=> ['required', 'in:CASH,PARTIAL'],
            'paid_amount'   => ['required', 'numeric', 'min:0'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
