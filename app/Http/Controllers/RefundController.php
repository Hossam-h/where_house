<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use Illuminate\Http\Request;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\PackinUserTaskRequest;
use Carbon\Carbon;
use Auth;
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
         $refunds = Refund::whereDate('created_at','>=',$lastMonth)->where('status','!=','approved')->paginate(request('limit')?? 15);
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
    public function assignTask(AssignTaskRequest $request,$id){

       $refund = Refund::findOrFail($id);


       if ($refund->packed_start_time !== NULL){
        return returnError('this task is already assigned');
       }
       
       $refund->update([
        'packed_start_time'    => $request->packed_start_time,
        'packed_supervisor_id' => Auth::guard('super_visors')->user()->id,
        'packed_user_id'       => $request->packed_user_id,
       ]);

       return returnSuccess(__('Task assigned succcess'));

    }


    public function PackingUserTask(Request $request,$id){

        $refund = Refund::findOrFail($id);

        $refund->update([
         'packed_end_time'      => date('Y-m-d H:i:s') ,
         'packed_qty'           => $request->packed_qty ?? null,
         'missing_qty'          => $request->packed_qty ?? null,
         'notes'                => $request->notes ?? null ,
         'status'               => 'approved' ,
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
