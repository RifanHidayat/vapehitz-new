<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CentralSale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CentralSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipments = Shipment::all();
        $centralSale = CentralSale::all();
        return view('central-sale.index', [
            'centralSale' => $centralSale,
            'shipment' => $shipments,
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
        $accounts = Account::all();
        $maxid = DB::table('central_sales')->max('id');
        $code = "SO/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);
        return view('central-sale.create', [
            'code' => $code,
            'customer' => $customers,
            'shipment' => $shipments,
            'accounts' => $accounts,
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
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
        $centralSale->subtotal = $request->subtotal;
        $centralSale->shipping_cost = $request->shipping_cost;
        $centralSale->other_cost = $request->other_cost;
        $centralSale->detail_other_cost = $request->detail_other_cost;
        $centralSale->deposit_customer = $request->deposit_customer;
        $centralSale->net_total = $request->net_total;
        $centralSale->receipt_1 = $request->receipt_1;
        $centralSale->receive_1 = $request->receive_1;
        $centralSale->receipt_2 = $request->receipt_2;
        $centralSale->receive_2 = $request->receive_2;
        $centralSale->recipient = $request->recipient;
        $centralSale->payment_amount = $request->payment_amount;
        $centralSale->remaining_payment = $request->remaining_payment;
        $centralSale->address_recipient = $request->address_recipient;
        $centralSale->detail = $request->detail;

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
        $customers = Customer::all();
        $accounts = Account::all();
        $shipments = Shipment::all();
        $centralsales = CentralSale::with(['products'])->findOrFail($id);
        $selectedProducts = collect($centralsales->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
            $product['price'] = $product->pivot->price;
            $product['stock'] = $product->pivot->stock;
            // $product['cause'] = 'defective';
        });

        return view('central-sale.edit', [
            'selected_products' => $selectedProducts,
            'central_sales' => $centralsales,
            'customers' => $customers,
            'accounts' => $accounts,
            'shipments' => $shipments,
        ]);
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

    public function datatableCentralSale()
    {
        $centralSale = CentralSale::with('products')->with('shipment')->select('central_sales.*');
        return DataTables::of($centralSale)
            ->addIndexColumn()
            ->addColumn('shipment_name', function ($row) {
                return ($row->shipment ? $row->shipment->name : "");
            })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                    <a href="/central-sale/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                        <span>Edit</span>
                    </a>
                    <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->make(true);
    }
}
