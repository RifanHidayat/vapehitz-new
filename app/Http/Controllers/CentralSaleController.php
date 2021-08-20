<?php

namespace App\Http\Controllers;

use App\Models\CentralSale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $centralSale = CentralSale::all();
        return view('central-sale.index', [
            'centralSale' => $centralSale,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        $maxid = DB::table('central_sales')->max('id');
        $code = "SO/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);
        return view('central-sale.create', [
            'code' => $code,
            'customer' => $customers,
            'shipment' => $shipments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $centralSale = new CentralSale;
        $centralSale->code = $request->code;
        $centralSale->date = $request->date . ' ' . date('H:i:s');
        $centralSale->customer_id = $request->customerId;
        $centralSale->shipment_id = $request->shipmentId;
        $centralSale->debt = $request->debt;

        try {
            $centralSale->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
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

    public function datatableProducts()
    {
        $products = Product::with('productCategory')->with('productSubcategory')->get();
        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('action', function () {
                $button = '<button class="btn btn-outline-primary btn-sm btn-choose"><em class="fas fa-plus"></em>&nbsp;Pilih</button>';
                return $button;
            })
            ->make(true);
    }
}
