<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovedFundPermits extends FormRequest
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
            'vichle_id'           =>'required|exists:vichles,id',
            'revision_start_time' =>'nullable|date_format:Y-m-d H:i:s',
            'revision_end_time'   =>'nullable|date_format:Y-m-d H:i:s',
        ];
    }
}
