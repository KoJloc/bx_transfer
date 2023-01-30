<?php

namespace App\Http\Requests\Persone;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'SECOND_NAME' => 'required|string',
            'LAST_NAME' => 'required|string',
            'ASSIGNED_BY_ID' => 'required|integer',
            'LEAD_ID' => 'required|integer',
        ];
    }
}
