<?php

namespace App\Http\Traits;
use Auth;
use App\Models\PackingUser;

trait SuperVisorId{
  public function  getSuperVisorId($id = null){

    $packedSupervisorId = null;
    if (Auth::guard('super_visors')->check())
    {
        $packedSupervisorId = Auth::guard('super_visors')->user()->id;
    }else{
        if(PackingUser::findOrFail($id)){
            $packingSupervisor  = PackingUser::findOrFail($id)->packingSupervisor;
            $packedSupervisorId = $packingSupervisor->id;
        }
    }

    return $packedSupervisorId;
}
}