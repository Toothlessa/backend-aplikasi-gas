<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DebtCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => ['numeric'],
            'description' => ['max:100'],
            'amount_pay'  => ['nullable'],
            'total'       => ['nullable'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
