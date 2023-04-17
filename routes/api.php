<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RefundController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




Route::group(['middleware' => ['api']], function () {

Route::post('Auth-login', [AuthController::class,'login']);
});


Route::group(['prefix' => 'Packing-user', 'middleware'=> ['auth:packings'] ],function ()
{
   Route::PUT('/packing-user-task/{id}',[RefundController::class,'PackingUserTask']);
});



Route::group(['prefix' => 'super_visors','middleware' => ['auth:super_visors']],function ()
{
   Route::resource('/refunds',RefundController::class);
   Route::PUT('/assign-task/{id}',[RefundController::class,'assignTask']);
});
