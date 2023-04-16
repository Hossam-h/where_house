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
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user_id' => Auth::guard('packings')->user()->id ?? Auth::guard('super_visors')->user()->id,
            'name' => Auth::guard('packings')->user()->name_ar ?? Auth::guard('super_visors')->user()->name_ar,
            'code' => Auth::guard('packings')->user()->code ?? Auth::guard('super_visors')->user()->code,
            'type' => isset(Auth::guard('packings')->user()->code) ? 'packing_user' : 'packing_supervisor' //api_supplier guard 
        ]);
    }

     // public function logout() {
    //     auth()->logout();
    //     return response()->json(['message' => 'User successfully signed out']);
    // }
}
