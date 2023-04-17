<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignTaskRequest extends FormRequest
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
            'packed_start_time'  =>'required|date_format:Y-m-d H:i:s|after:yesterday',
            'packed_user_id'     =>'required|Integer|exists:packing_users,id',
        ];
    }
}
