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



Route::group(['prefix' => 'Packing-user', 'middleware'=> ['auth:packings'] ],function ()
{
   Route::PUT('/refund-task/{id}',[RefundController::class,'PackingUserTask']);
   Route::PUT('/fund-permit-task/{id}',[FundPermitController::class,'PackingUserFundTask']);
   Route::get('/fund-permits-tasks',[FundPermitController::class,'fundPermitTasks']);
   Route::PUT('/assign-task-fund-permit/{id}',[FundPermitController::class,'assignTask']);

   ////////////////////////////// refunds //////////////////////////////////////////////////

   Route::get('/refunds',[RefundController::class,'index']);
   Route::get('/task-refund-create/{id}',[RefundController::class,'edit']);
   Route::PUT('/assign-task-refund/{id}',[RefundController::class,'assignTask']);
});
