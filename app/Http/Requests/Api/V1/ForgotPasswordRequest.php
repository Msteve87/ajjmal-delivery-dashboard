<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'phone' => [
                'required',
                'exists:drivers,phone',
                'numeric',
                'starts_with:21891,21892,21893,21894',
                'digits:12'
            ],
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
