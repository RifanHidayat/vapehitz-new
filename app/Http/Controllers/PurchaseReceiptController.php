<?php

namespace App\Http\Controllers;

use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReturn;
use App\Models\RequestToRetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      //  return view ()

        
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
        $maxid = DB::table('purchase_receipts')->max('id');
        $code = "PRR/VH/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);
        $purchaseReceipt = new PurchaseReceipt();
        $purchaseReceipt->code=$code;
        $purchaseReceipt->date=$request->date;
        $purchaseReceipt->quantity=$request->total_return_quantity;
        $purchaseReceipt->free=$request->total_return_free;
        $purchaseReceipt->central_purchase_id=$request->purchase_id;
        $purchaseReceipt->supplier_id=$request->supplier_id;
        $purchaseReceipt->note=$request->note;
        $products=$request->products;
        
        try{
            $purchaseReceipt->save();
        }catch(Exception $e){
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

       
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
               $productRow->central_stock =  $productRow->central_stock + $product['initial_quantity'] + $product['initial_free'];
              
               $productRow->save();         
            }    
        } catch (Exception $e) {
            // $centralPurchase->products()->detach();
            // $centralPurchase->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => '1'.$e,
            ], 500);
        }   


        $keyedProducts = collect($products)->filter(function ($item){
            return ($item['initial_quantity']!=0) || ($item['initial_free']!==0) ;
        })->mapWithKeys(function ($item) {
           // if (($item['return_quantity']!=0) || $item['free']!==0 ){
                return [
                    $item['id'] => [
                        'quantity' => $item['initial_quantity'],
                        'free' => $item['initial_free'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
                
            //}
           
        })->all();
    
        try {
            $purchaseReceipt->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $purchaseReceipt->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => ''.$e,
            ], 500);
        }  
      
   



    //save produk purchase return
   
       

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

       
    }


    public function approved(Request $request, $id)
    {

    }

    public function rejected(Request $request, $id)
    {
  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


}
