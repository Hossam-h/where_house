<?php

namespace App\Http\Controllers;

use App\Models\FundPermit;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\PackingUser;
use Illuminate\Http\Request;
use App\Http\Requests\AssignFundTaskRequest;
use App\Http\Requests\FinishedTaskRequest;
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
        $delivries = Delivery::paginate(request('limit')?? 15);
        return returnPaginatedData([$delivries]);
    }



    public function fundPermitTasks(){

        $refundPermits = FundPermit::paginate(request('limit')?? 15);
        return returnPaginatedData([$refundPermits]);
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
         'missing_qty'        => $request->missing_qty ?? null,
         'notes'              => $request->notes ?? null ,
        ]);

        return returnSuccess(__('Task finished succcess'));
    }

    public function assignTask(AssignFundTaskRequest $request,$id){

        $product_id = $request->product_id;
        if(Product::findOrFail($product_id)->stock < $request->packed_qty){
            return returnError('this qty is not availaible');
        }
        $product = Product::findOrFail($product_id);
        
        DB::beginTransaction();

        FundPermit::create([
            'packed_start_time'    => $request->packed_start_time,
            'packed_supervisor_id' => Auth::guard('super_visors')->user()->id,
            'packed_qty'           => $request->packed_qty,
            'packed_user_id'       => $request->packed_user_id,
            'delivery_id'          => $id,
            'product_id'           => $product_id,
            'is_assigned'          => 1 ,
        ]);

        $product->update([
          'stock' =>  $product->stock - $request->packed_qty
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
        $products = Product::select('id','title_ar')->get();

        return  ['packingUsers'=> $packingUser, 'products'=>$products] ;    
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
}
