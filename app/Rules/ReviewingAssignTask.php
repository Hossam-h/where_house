<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Auth;
use App\Models\FundPermit;
use App\Models\Refund;

class ReviewingAssignTask implements Rule
{

    protected $message;
    protected $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct($id)
    {
        $this->id = $id;
        
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
        $SuperVisorId          = Auth::guard('super_visors')->user()->id;
        $fundPermit            = FundPermit::where([['packed_supervisor_id',$SuperVisorId],['start_time_revision','!=',null],['end_time_revision',null]])->first();
        $fundPermitAvailable   = FundPermit::where([['id',$this->id ],['start_time_revision','!=',null],['end_time_revision',null]])
                                          ->orWhere([['id',$this->id ],['start_time_revision','!=',null],['end_time_revision',null]]);
        //$refund       = Refund::where([['packed_supervisor_id',$SuperVisorId],['start_time_revision',null]])->first();
        if($fundPermit !== null){ 
        $this->message =  'this Supervisor already have a task';
         return false;
       }elseif( $fundPermitAvailable != null){
        $this->message =  'this Fund permits already in Reviewng';
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
