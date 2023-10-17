<?php

namespace App\Http\Controllers;

use App\Models\{FundPermit,Delivery,Product,PackingUser,ProductUnit,Unit,Refund,FundPermitProduct};
use Illuminate\Http\Request;
use App\Http\Requests\AssignFundTaskRequest;
use App\Http\Requests\FinishedTaskRequest;
use App\Http\Requests\ApprovedFundPermits;
use App\Http\Resources\FundPermitResource;
use App\Http\Traits\SuperVisorId;
use App\Http\Requests\ReviewingAssignTaskRequest;
use Auth;
use DB;

class FundPermitController extends Controller
{
    use SuperVisorId;
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
     
        $fundPermits  = FundPermit::with(['fundPermitProducts'=>function($q){
        $q->selectRaw('id,fund_permit_id, product_id, SUM(quantity) as quantity, SUM(packed_qty) as packed_qty, SUM(missing_qty) as missing_qty, SUM(cost) as cost')
            ->groupBy(['id']);
     }],'products.units:id,name_ar,name_en','delivery:id,name_ar','packingUser:id,name_ar')
     ->orderBy('id', 'DESC')->select('id',
                         'packed_user_id',
                         'delivery_id',
                         'cost',
                         'packed_start_time',
                         'packed_end_time',
                         'created_at',
                         'status')->orderBy('id', 'DESC')->dailyFilter()->paginate(request('limit') ?? 15);

        return returnPaginatedResourceData(FundPermitResource::collection($fundPermits));
    }


    public function packedFundPermits(){
        $fundPermits    = FundPermit::orderBy('id', 'DESC')->where('status','packed')->dailyFilter()->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(FundPermitResource::collection($fundPermits));
    }

    public function approvedFundPermits(ApprovedFundPermits $request,$id){
       $fundPermit =   FundPermit::findOrFail($id);
       DB::beginTransaction();

       $fundPermit->update([
           'status'               => 'approved',
           'vichle_id'            => $request->vichle_id,
           'end_time_revision'    => date('Y-m-d H:i:s') ?? null, 
           'packed_supervisor_id' => $this->getSuperVisorId(),
        ]);

        if(count($request->products) > 0){
            foreach($request->products as $product){
                if(isset($product['revision_quantity'])){
                    FundPermitProduct::findOrFail($product['fund_permit_product_id'])->update([
                        'revision_quantity' => $product['revision_quantity'] ?? null,
                        'comment'           => $product['comment'] ?? null,
                        'revision_comment'  => $product['revision_comment'] ?? null,
                    ]);
                }
            }
        }
        DB::commit();

        
        return returnSuccess(__('Task Approved succcess'));

    }

    public function PackingUserFundTask(FinishedTaskRequest $request , $id){

        $fundPermit = FundPermit::findOrFail($id);

        DB::beginTransaction();

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
        DB::commit();


        return returnSuccess(__('Task finished succcess'));
    }

    public function assignTask(AssignFundTaskRequest $request,$id){

        $fundPermit = FundPermit::findOrFail($id);

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

    public function reviewingAssignTask(ReviewingAssignTaskRequest $request,$id){
        $fundPermit = FundPermit::findOrFail($id);
        $fundPermit->update([
            'start_time_revision'  => date('Y-m-d H-i-s'),
            'status'               => 'in_reviewing',
            'packed_supervisor_id' => $this->getSuperVisorId(),
        ]);
        return returnSuccess(__('Task In Reviewing'));
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

}
