<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\JsonResponse;
use App;
use Auth;
use Log;
use App\Http\Controllers\Controller;
use App\Requests\LoginValidation;
use App\Http\Resources\{RefundResource,FundPermitResource};
use Hash;
class AuthController extends Controller
{

    public function login(Request $request)
    {

        try {
       $token = null;   

            $packingAuth = Auth::guard('packings')->attempt([
                'code' => $request->input('code'), 
                'password' => $request->input('password'),
            ]);


            $superVisorsAuth = Auth::guard('super_visors')->attempt([
                'code' => $request->input('code'), 
                'password' => $request->input('password'),
            ]);


            if(!$superVisorsAuth && !$packingAuth){
                return response()->json([
                    'errors' => [
                        'code' => ['Your code and/or password may be incorrect.']
                    ]
                ], 422);
            }elseif($superVisorsAuth){
                $token  = $superVisorsAuth;
            }elseif($packingAuth){
                $token  = $packingAuth;
            }

        } catch (JWTException $e) {
            return response()->json(['message' => 'Could not create token!'], 401);
        }

        return $this->respondWithToken($token);
    }

   

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token'      => $token,
            'token_type'        => 'bearer',
            'expires_in'        => auth()->factory()->getTTL() *60*24,
            'user_id'           => Auth::guard('packings')->user()->id ?? Auth::guard('super_visors')->user()->id,
            'name'              => Auth::guard('packings')->user()->name_ar ?? Auth::guard('super_visors')->user()->name_ar,
            'code'              => Auth::guard('packings')->user()->code ?? Auth::guard('super_visors')->user()->code,
            'type'              => isset(Auth::guard('packings')->user()->code) ? 'packing_user' : 'packing_supervisor', //api_supplier guard 
            'refund_tasks'      => $this->refundTasks(),
            'fund_permit_tasks' => $this->fundPermitTasks()
        ]);
    }

    public function refundTasks(){
        $refundTasks = null;
        if(Auth::guard('packings')->user()){
            $refundTasks =  Auth::guard('packings')->user()->load(['refunds'=>function($q){
                $q->where('packed_end_time',null);
            }])->refunds;
        }else{
            $refundTasks =  Auth::guard('super_visors')->user()->load(['refunds'=>function($q){
                $q->where('packed_end_time',null);
            }])->refunds;
        }
        return returnPaginatedResourceData(RefundResource::collection($refundTasks));
       
    }


    public function fundPermitTasks(){
        $fundPermitTasks = null;
        
        if(Auth::guard('packings')->user()){
            $fundPermitTasks =  Auth::guard('packings')->user()->load(['fundPermits'=>function($q){
                $q->where('packed_end_time',null);
            }])->fundPermits;
        }else{
            $fundPermitTasks =  Auth::guard('super_visors')->user()->load(['fundPermits'=>function($q){
                $q->where('packed_end_time',null);
            }])->fundPermits;
        }
        return returnPaginatedResourceData(FundPermitResource::collection($fundPermitTasks));
       
    }

     // public function logout() {
    //     auth()->logout();
    //     return response()->json(['message' => 'User successfully signed out']);
    // }
}
