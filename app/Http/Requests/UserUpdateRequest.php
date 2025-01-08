<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username'  => ['nullable', 'max:100'],
            'password'  => ['nullable', 'max:100'],
            'fullname'  => ['nullable', 'max:100'],
            'email'     => ['nullable', 'email', 'max:50'],
            'phone'     => ['nullable', 'max:20'],
            'street'    => ['nullable', 'max:100'],
            'city'      => ['nullable', 'max:50'],
            'province'  => ['nullable', 'max:50'],
            'postal_code' => ['nullable', 'max:20'],
            'country'   => ['nullable', 'max:50'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
