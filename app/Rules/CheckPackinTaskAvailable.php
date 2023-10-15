<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;
use App\Models\FundPermit;
use App\Models\Refund;

class CheckPackinTaskAvailable implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $message;
    public function __construct()
    {
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $packedUserId = $id ?? Auth::guard('packings')->user()->id;
        $fundPermit   = FundPermit::where([['packed_user_id',$packedUserId],['packed_end_time',null]])->first();
        $refund       = Refund::where([['packed_user_id',$packedUserId],['packed_end_time',null]])->first();
        if($fundPermit !== null || $refund !== null){ 
           
        $this->message =  'this packing User already have a task';
         return false;
       }
       return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
