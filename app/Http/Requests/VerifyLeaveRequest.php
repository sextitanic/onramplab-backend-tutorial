<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyLeaveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'approver' => 'required|string|max:45'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $transformed = [];
        $errors = $validator->errors()->toArray();
        throw new HttpResponseException(
            response()->json(
                [
                    'code' => '422',
                    'success' => false,
                    'message' => 'validation failed',
                    'data' => $errors,
                ],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            )
        );
    }
}
