<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\FundPermitController;

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
   Route::PUT('/refund-task/{id}',[RefundController::class,'PackingUserTask']);
   Route::PUT('/fund-permit-task/{id}',[FundPermitController::class,'PackingUserFundTask']);

});



Route::group(['prefix' => 'super_visors','middleware' => ['auth:super_visors']],function ()
{
   Route::resource('/refunds',RefundController::class)->except('edit');
   Route::get('/task-refund-create/{id}',[RefundController::class,'edit']);
   Route::PUT('/assign-task-refund/{id}',[RefundController::class,'assignTask']);

   Route::get('/fund-permits',[FundPermitController::class,'index']);
   Route::get('/fund-permits-tasks',[FundPermitController::class,'fundPermitTasks']);
   Route::get('/task-fund-permit-create/{id}',[FundPermitController::class,'edit']);
   Route::PUT('/assign-task-fund-permit/{id}',[FundPermitController::class,'assignTask']);

});
