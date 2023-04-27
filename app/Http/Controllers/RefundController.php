<?php

namespace App\Http\Controllers;

use App\Models\{Refund,PackingUser,RefundProduct};
use Illuminate\Http\Request;
use App\Http\Requests\AssignTaskRequest;
use App\Http\Requests\PackinUserTaskRequest;
use Carbon\Carbon;
use Auth;
use App\Http\Resources\RefundResource;
use DB;
class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

         $lastMonth  =  Carbon::createFromFormat('m/d/Y',Carbon::now()->format('m/d/Y'))->subMonth()->format('Y-m-d');
         $refunds    = Refund::whereDate('created_at','>=',$lastMonth)->paginate(request('limit') ?? 15);
         return returnPaginatedResourceData(RefundResource::collection($refunds));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
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

        DB::beginTransaction();

        $refund->update([
         'packed_end_time'   => date('Y-m-d H:i:s') ,
         'notes'             => $request->notes ?? null ,
         'status'            => 'approved' ,
        ]);

         foreach ($request->products as $product) {
            RefundProduct::findOrFail($product['refund_product_id'])->update([
                'packed_qty'        => $product['packed_qty'] ?? null,
                'missing_qty'       => $product['missing_qty'] ?? null,
            ]);
         }

         DB::commit();

        return returnSuccess(__('Task finished succcess'));
     }


   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Refund  $refund
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $packingUser = PackingUser::select('id','name_ar')->paginate(request('limit')?? 15);
        return returnPaginatedData([$packingUser]);    
    }

   
}
