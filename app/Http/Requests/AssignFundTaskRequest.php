<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckPackinTaskAvailable;
class AssignFundTaskRequest extends FormRequest
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
            'packed_start_time'    => ['required','date_format:Y-m-d H:i:s','after:yesterday',new CheckPackinTaskAvailable()],
            'packed_supervisor_id' => 'exists:packing_supervisors,id',
            'packed_user_id'       => 'exists:packing_users,id',
            'delivery_id'          => 'integer',
            "products*"            => "required|array",

        ];
    }
}
