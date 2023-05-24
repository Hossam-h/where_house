<?php

namespace App\Http\Controllers;

use App\Models\{FundPermit,Delivery,Product,PackingUser,ProductUnit,Unit,Refund,FundPermitProduct};
use Illuminate\Http\Request;
use App\Http\Requests\AssignFundTaskRequest;
use App\Http\Requests\FinishedTaskRequest;
use App\Http\Resources\FundPermitResource;
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

        $fundPermits    = FundPermit::orderBy('id', 'DESC')->paginate(request('limit') ?? 15);
        return returnPaginatedResourceData(FundPermitResource::collection($fundPermits));
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
         'status'             => $request->status ?? 'approved' ,
        ]);
        
        if(count($request->products) > 0){
            foreach($request->products as $product){
                if($product['missing_qty']|| $product['packed_qty']){
                    FundPermitProduct::findOrFail($product['fund_permit_product_id'])->update([
                        'missing_qty' => $product['missing_qty'] ?? null,
                        'packed_qty'  => $product['packed_qty'] ?? null,

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

        DB::beginTransaction();
            $fundPermit->update([
                    'packed_start_time'    => $request->packed_start_time,
                    'packed_supervisor_id' => Auth::guard('super_visors')->user()->id,
                    'packed_user_id'       => $request->packed_user_id,
                    'is_assigned'          => 1 ,
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
    
        $fundPermit = FundPermit::where([['packed_user_id',$id],['packed_end_time',null]])->first();
        $refund     = Refund::where([['packed_user_id',$id],['packed_end_time',null]])->first();

       if($fundPermit != null || $refund != null){        
         return true;
       }
       return false;

    }
}
