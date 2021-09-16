<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use Exception;
use Illuminate\Http\Request;

class ReturSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('retur-supplier.index');
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
        $centralPurchase = new CentralPurchase();
        $centralPurchase->code = $request->code;
        $centralPurchase->date = $request->date;
        $centralPurchase->supplier_id = $request->supplier_id;
        $centralPurchase->account_id = $request->account_id;
        $centralPurchase->total = $request->total;
        $centralPurchase->shipping_cost = str_replace(".", "", $request->shipping_cost);
        $centralPurchase->discount = str_replace(".", "", $request->discount);
        $centralPurchase->netto = $request->netto;
        $centralPurchase->pay_amount = $request->pay_amount;
        $centralPurchase->payment_method = $request->payment_method;
        $products = $request->selected_products;

        // //Transaction account
        // $accountTransaction=new AccountTransaction;
        // $accountTransaction->account_out=$request->account_id;
        // $accountTransaction->amount=$request->pay_amount;
        // $accountTransaction->type="out";
        // $accountTransaction->note="Pembelian barang dengan code ".$request->code;
        // $accountTransaction->date=$request->date;

        //Transaction account Shipping Cost
        // $accountTransactionShippingCost=new AccountTransaction;
        // $accountTransactionShippingCost->account_in="1";
        // $accountTransactionShippingCost->amount=$request->pay_amount;
        // $accountTransactionShippingCost->type="in";
        // $accountTransactionShippingCost->note="Biaya kirim Pembelian barang dengan No. Order ".$request->code;
        // $accountTransactionShippingCost->date=$request->date;
      
        // //Transaction account debt
        // $accountTransactionDebt=new AccountTransaction;
        // $accountTransactionDebt->account_in="3";
        // $accountTransactionDebt->amount=$request->pay_amount;
        // $accountTransactionDebt->type="in";
        // $accountTransactionDebt->note="Hutang Pembelian barang dengan No. Order ".$request->code;
        // $accountTransactionDebt->date=$request->date;

    

           
                
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
