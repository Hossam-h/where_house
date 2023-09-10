<?php

namespace App\Http\Controllers;

use App\Models\{FundPermit,Delivery,Product,PackingUser,ProductUnit,Unit,Refund,Vichle,FundPermitProduct};
use Illuminate\Http\Request;
use App\Http\Requests\AssignFundTaskRequest;
use App\Http\Requests\FinishedTaskRequest;
use App\Http\Requests\ApprovedFundPermits;
use App\Http\Resources\FundPermitResource;
use App\Http\Resources\VichleResource;
use Auth;
use DB;


class FundPermitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $delivries = Delivery::orderBy('id', 'DESC')->paginate(request('limit')?? 15);
        return returnPaginatedData([$delivries]);
    }

    public function fundPermitTasks(){
        $fundPermits    = FundPermit::orderBy('id', 'DESC')->dailyFilter()->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(FundPermitResource::collection($fundPermits));
    }


    public function packedFundPermits(){
        $fundPermits    = FundPermit::orderBy('id', 'DESC')->where('status','packed')->dailyFilter()->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(FundPermitResource::collection($fundPermits));
    }

    public function approvedFundPermits(ApprovedFundPermits $request,$id){
       $fundPermit =   FundPermit::findOrFail($id);
       
       $fundPermit->update([
           'status'               => 'approved',
           'vichle_id'            => $request->vichle_id,
           'revision_start_time'  => $request->revision_start_time ?? null,
           'revision_end_time'    => $request->revision_end_time ?? null, 
           'packed_supervisor_id' => $this->getSuperVisorId(),
        ]);

        if(count($request->products) > 0){
            foreach($request->products as $product){
                if(isset($product['revision_quantity'])){
                    FundPermitProduct::findOrFail($product['fund_permit_product_id'])->update([
                        'revision_quantity' => $product['revision_quantity'] ?? null,
                        'comment'           => $product['comment'] ?? null,
                    ]);
                }
            }
        }
        
        return returnSuccess(__('Task Approved succcess'));

    }


    public function allVichles(){
        $vichles = Vichle::orderBy('id', 'DESC')->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(VichleResource::collection($vichles));
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

    public function PackingUserFundTask(FinishedTaskRequest $request , $id){

        $fundPermit = FundPermit::findOrFail($id);

        $fundPermit->update([
         'packed_end_time'    => date('Y-m-d H:i:s') ,
         'notes'              => $request->notes ?? null ,
         'status'             => $request->status ?? 'packed' ,
        ]);
        
        if(count($request->products) > 0){
            foreach($request->products as $product){
                if($product['missing_qty']|| $product['packed_qty']){
                    FundPermitProduct::findOrFail($product['fund_permit_product_id'])->update([
                        'missing_qty' => $product['missing_qty'] ?? null,
                        'packed_qty'  => $product['packed_qty'] ?? null,
                        'comment'     => $request->comment ?? null,
                    ]);
                }
            }
        }

        return returnSuccess(__('Task finished succcess'));
    }

    public function assignTask(AssignFundTaskRequest $request,$id){

        $fundPermit = FundPermit::findOrFail($id);

        if($this->checkPackinTaskAvailable($request->packed_user_id)){
            return returnError('this packing User already have a task');
        }

        if ($fundPermit->packed_start_time !== NULL){
            return returnError('this task is already assigned');
           }

        $packedUserId = $request->packed_user_id ?? Auth::guard('packings')->user()->id;
        DB::beginTransaction();
            $fundPermit->update([
                    'packed_start_time'    => $request->packed_start_time,
                    'packed_supervisor_id' => $this->getSuperVisorId($packedUserId),
                    'packed_user_id'       => $packedUserId,
                    'is_assigned'          => 1 ,
                    'status'               => 'in_picking' ,
                ]);

        DB::commit();
 
        return returnSuccess(__('Task assigned succcess'));
     }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FundPermit  $fundPermit
     * @return \Illuminate\Http\Response
     */
    public function show(FundPermit $fundPermit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FundPermit  $fundPermit
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $packingUser = PackingUser::select('id','name_ar')->get();
        $products    = Product::select('id','title_ar')->get();
        $units       = Unit::select('id','name_ar')->get();

        return  ['packingUsers'=> $packingUser, 'products'=>$products, 'units'=>$units];    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FundPermit  $fundPermit
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FundPermit $fundPermit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FundPermit  $fundPermit
     * @return \Illuminate\Http\Response
     */
    public function destroy(FundPermit $fundPermit)
    {
        //
    }

    public function checkPackinTaskAvailable($id){
         
        $packedUserId = $id ?? Auth::guard('packings')->user()->id;
        $fundPermit = FundPermit::where([['packed_user_id',$packedUserId],['packed_end_time',null]])->first();
        $refund     = Refund::where([['packed_user_id',$packedUserId],['packed_end_time',null]])->first();

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
