<?php

namespace App\Http\Controllers;

use App\Models\{Refund,PackingUser,FundPermit,RefundProduct,Delivery,ProductLockedStock};
use Illuminate\Http\Request;
use App\Http\Requests\{AssignTaskRequest,PackinUserTaskRequest};
use Carbon\Carbon;
use Auth;
use App\Http\Resources\{RefundResource,DeliveryResource};
use DB;
class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public $fudPermit;

     public function __construct(FundPermit $fudPermit){

        $this->fudPermit = $fudPermit;
     }

    public function index()
    {
        $Deliveryrefunds    = Delivery::whereHas('refunds',function($q){
        $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        })->orWhereHas('refundsPartial',function($q){
            $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        })->orWhereHas('oldRefund',function($q){
            $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        })->with(['refunds'=>function($q){
            $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        }])->with(['refundsPartial'=>function($q){
            $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        }])->with(['oldRefund'=>function($q){
            $q->whereDate('created_at', Carbon::today()->format('Y-m-d'));
        }])->get();
      
        return returnPaginatedResourceData(DeliveryResource::collection($Deliveryrefunds));
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

        if($this->checkPackinTaskAvailable($request->packed_user_id))
        {
            return returnError('this packing User already have a task');
        }

       if ($refund->packed_start_time !== NULL){
        return returnError('this task is already assigned');
       }
       
       $packedUserId = $request->packed_user_id ?? Auth::guard('packings')->user()->id;

       $refund->update([
        'packed_start_time'    => $request->packed_start_time,
        'packed_supervisor_id' => $this->getSuperVisorId($packedUserId),
        'packed_user_id'       => $packedUserId,
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
            $refundProduct = RefundProduct::findOrFail($product['refund_product_id']);
            if(!$product['is_missing']){
                $refundProduct->update([
                    'packed_qty'  => $product['packed_qty'] ?? null,
                    'missing_qty' => $product['missing_qty'] ?? null,
                ]);
                $refundProduct->product->increment('for_sell_quantity', $product['packed_qty']);
            }else{
                $locked_stock = ProductLockedStock::whereDate('locked_stock_date',date('Y-m-d'))->first();
                $refundProduct->update([
                    'packed_qty'  => $product['packed_qty'] ?? null,
                    'missing_qty' => $product['missing_qty'] ?? null,
                ]);
                if ($locked_stock) {
                    $locked_stock->increment('locked_stock_qty', $product['missing_qty']);
                }else{
                    ProductLockedStock::create([
                        'locked_stock_date'     => date('Y-m-d'),
                        'locked_stock_qty' => $product['missing_qty'],
                        'product_id' => $refundProduct->product_id
                    ]);
                }
            }
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
        $packingUser = PackingUser::select('id','name_ar')->orderBy('id', 'DESC')->paginate(request('limit')?? 15);
        return returnPaginatedData([$packingUser]);    
    }

    public function checkPackinTaskAvailable($id){
        $fundPermit = FundPermit::where([['packed_user_id',$id],['packed_end_time',null]])->first();
        $refund     = Refund::where([['packed_user_id',$id],['packed_end_time',null]])->first();

        if($fundPermit != null || $refund != null){
            return true;
          }
          return false;
    }

    public function getSuperVisorId($id = null)
    {
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
