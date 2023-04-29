<?php

namespace App\Http\Controllers;

use App\Models\{FundPermit,Delivery,Product,PackingUser,ProductUnit,FundPermitProduct};
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
        ]);
        
        if(count($request->products) > 0){
            foreach($request->products as $product){
                if($product['missing_qty']){
                    FundPermitProduct::findOrFail($product['fund_permit_product_id'])->update([
                        'missing_qty' => $product['missing_qty'] ?? null
                    ]);
                }
            }
        }

        return returnSuccess(__('Task finished succcess'));
    }

    public function assignTask(AssignFundTaskRequest $request,$id){

    
        
        DB::beginTransaction();

            $fundPermit = FundPermit::create([
                    'packed_start_time'    => $request->packed_start_time,
                    'packed_supervisor_id' => Auth::guard('super_visors')->user()->id,
                    'packed_user_id'       => $request->packed_user_id,
                    'delivery_id'          => $id,
                    'is_assigned'          => 1 ,
                    'cost'                 => 0 ,
                ]);

                $fundPermitCost = null;

                    foreach ($request->products as $product)
                    {
                        $Fundproduct = Product::findOrFail($product['product_id']);
                        if( $Fundproduct->stock < $product['packed_qty']){
                            return returnError('this qty is not available in this product'.$Fundproduct->title_ar);
                        }
                        FundPermitProduct::create([
                            'quantity'        => $product['packed_qty'] ?? null,
                            'product_id'      => $product['product_id'] ?? null,
                            'fund_permit_id'  => $fundPermit->id,
                            'price'           => ProductUnit::where([ 
                                                ['unit_id',$product['unit_id']] ,
                                                ['product_id',$product['product_id']] 
                                                ])->first()->price ,
                            'cost'            => $product['packed_qty'] *  ProductUnit::where([ 
                                                                        ['unit_id',$product['unit_id']], 
                                                                        ['product_id',$product['product_id']]
                                                                        ])->first()->price,
                            ]);


                            $fundPermitCost   +=  $product['packed_qty'] *  ProductUnit::where([ 
                                                                        ['unit_id',$product['unit_id']], 
                                                                        ['product_id',$product['product_id']]
                                                                        ])->first()->price;

                        $Fundproduct->update([
                                'stock' => $Fundproduct->stock - $product['packed_qty']
                        ]);

                    }

                $fundPermit->update([
                'cost' =>   $fundPermitCost
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
}
