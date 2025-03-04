<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reset_token' => 'required',
            'password' => 'required|confirmed',
            Password::min(8),
            Password::min(8)->letters(),
            Password::min(8)->numbers(),
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            Response::json([
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
