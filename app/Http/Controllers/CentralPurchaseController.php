<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $centralpurchase = CentralPurchase::all();

        return view('central-purchase.index', [
            'centralpuchases' => $centralpurchase,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $maxid = DB::table('central_purchases')->max('id');
        $code = "PO/VH/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);
        return view('central-purchase.create', [
            'code' => $code,
            'suppliers' => $suppliers,
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
        $centralPurchase = new CentralPurchase;
        $centralPurchase->code = $request->code;
        $centralPurchase->date = $request->date;
        $centralPurchase->supplier_id = $request->supplier_id;
        $centralPurchase->account_id = $request->account_id;
        $centralPurchase->total = $request->total;
        $centralPurchase->shipping_cost = $request->shipping_cost;
        $centralPurchase->discount = $request->discount;
        $centralPurchase->netto = $request->netto;
        $centralPurchase->pay_amount = $request->pay_amount;
        $centralPurchase->payment_method = $request->payment_method;

        $products = $request->selected_products;

        try {
            $centralPurchase->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'price' => $item['purchase_price'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $centralPurchase->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralPurchase,
            // ]);
        } catch (Exception $e) {
            $centralPurchase->delete();
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

                $productRow->central_stock = $productRow->central_stock + $product['quantity'];
                // Calculate average purchase price
                $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $product['purchase_price'])) / ($productRow->central_stock + $product['quantity']);
                $productRow->purchase_price = round($newPrice);
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralPurchase,
            ]);
        } catch (Exception $e) {
            $centralPurchase->products()->detach();
            $centralPurchase->delete();
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


    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatableProducts(Request $request)
    {
        // $customerId = $request->query('customer_id');
        // $users = User::select(['id', 'name', 'email', 'created_at', 'updated_at']);
        $products = Product::with(['productCategory'])->select('products.*');

        return DataTables::eloquent($products)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '<button class="btn btn-outline-primary btn-sm btn-choose"><em class="fas fa-plus"></em>&nbsp;Pilih</button>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    private function clearThousandFormat($number)
    {
        return str_replace(".", "", $number);
    }
}
