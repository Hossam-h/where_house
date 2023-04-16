<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Lang;
class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $lastMonth =  Carbon::createFromFormat('m/d/Y',Carbon::now()->format('m/d/Y'))->subMonth()->format('Y-m-d');
         $refunds = Refund::whereDate('created_at','>=',$lastMonth)->paginate(request('limit')?? 15);
         return returnPaginatedData([$refunds]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignTask(Request $request,$id){

       return __('auth.password');
        dd(Carbon::now());
       $refund = Refund::findOrFail($id);
       $refund->update([
        'packed_start_time'  =>Carbon::now() ,
       ]);
       return returnSuccess(__('Task assigned succcess'));

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function show(Refund $refund)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function edit(Refund $refund)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Refund $refund)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function destroy(Refund $refund)
    {
        //
    }
}
