<?php

namespace App\Http\Requests;

use App\Http\Requests\APIRequest;

class ApiLoginRequest extends APIRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|max:255|string',
            'password' => 'required'
        ];
    }
}
