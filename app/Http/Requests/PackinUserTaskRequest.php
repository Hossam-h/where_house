<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackinUserTaskRequest extends FormRequest
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
            'packed_end_time'  => 'date_format:Y-m-d H:i:s',
            'packed_qty'       => 'required|Integer',
            'missing_qty'      => 'Integer',
            'notes'            => 'text',
        ];
    }
}
