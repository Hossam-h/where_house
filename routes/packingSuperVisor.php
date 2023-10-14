<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RefundController;
use App\Http\Controllers\FundPermitController;
use App\Http\Controllers\VichleController;

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






Route::group(['prefix' => 'super_visors','middleware' => ['auth:super_visors']],function ()
{
   Route::resource('/refunds',RefundController::class)->except('edit','update','destroy','show');
   Route::get('/task-refund-create/{id}',[RefundController::class,'edit']);
   Route::PUT('/assign-task-refund/{id}',[RefundController::class,'assignTask']);

   Route::get('/fund-permits',[FundPermitController::class,'index']);
   Route::get('/fund-permits-tasks',[FundPermitController::class,'fundPermitTasks']);
   Route::get('/task-fund-permit-create/{id}',[FundPermitController::class,'edit']);
   Route::PUT('/assign-task-fund-permit/{id}',[FundPermitController::class,'assignTask']);


   //supervisor-reviewer-to-approved
   Route::get('/packed-fund-permits',[FundPermitController::class,'packedFundPermits']);
   Route::PUT('/approved-fund-permits/{id}',[FundPermitController::class,'approvedFundPermits']);

   //all vichles
   Route::get('/all-vichles',[VichleController::class,'index']);

});
