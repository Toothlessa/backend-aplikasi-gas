<?php

namespace App\Http\Requests\Debt;

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
            'amount_pay'  => ['numeric', 'nullable'],
            'total'       => ['numeric', 'nullable'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
