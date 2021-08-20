<?php

namespace App\Http\Controllers;

use App\Models\Account;
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
        $centralpurchases = CentralPurchase::all();
        $suppliers = Supplier::all();
        return view('central-purchase.index', [
            'centralpurchases' => $centralpurchases,
            'suppliers' => $suppliers,
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
        $accounts = Account::all();
        $maxid = DB::table('central_purchases')->max('id');
        $code = "PO/VH/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);
        return view('central-purchase.create', [
            'code' => $code,
            'suppliers' => $suppliers,
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
        $centralPurchase = new CentralPurchase;
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

                // Calculate average purchase price
                $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $product['purchase_price'])) / ($productRow->central_stock + $product['quantity']);
                $productRow->purchase_price = round($newPrice);
                $productRow->central_stock = $productRow->central_stock + $product['quantity'];
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
        $centralPurchase = CentralPurchase::with(['products'])->findOrFail($id);
        return view('central-purchase.show', [
            'centralPurchase' => $centralPurchase,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $suppliers = Supplier::all();
        $accounts = Account::all();
        $centralpurchases = CentralPurchase::with(['products'])->findOrFail($id);

        return view('central-purchase.edit', [
            'central_purchases' => $centralpurchases,
            'suppliers' => $suppliers,
            'accounts' => $accounts,
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
        $centralpurchase = CentralPurchase::findOrFail($id);
        try {
            $centralpurchase->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralpurchase,
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

    public function pay($id)
    {
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $accounts = Account::all();

        // return $purchase;

        // foreach($purchase->products)
        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['quantity'] = $product->pivot->quantity;
        //     $product['purchase_price'] = $product->pivot->price;
        // });

        // return $selectedProducts;
        $transactions = collect($purchase->purchaseTransactions)->sortBy('date')->values()->all();

        return view('central-purchase.pay', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'transactions' => $transactions,
        ]);
    }

    public function return($id)
    {
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $accounts = Account::all();

        $selectedProducts = collect($purchase->products)->each(function ($product) {
            $product['return_quantity'] = 1;
            $product['cause'] = 'defective';
        });

        return view('central-purchase.return', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
        ]);
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

    public function datatableCentralPurchase()
    {
        $centralPurchase = CentralPurchase::with(['supplier'])->select('central_purchases.*');
        return DataTables::eloquent($centralPurchase)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return ($row->supplier ? $row->supplier->name : "");
            })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                    <a href="/central-purchase/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                        <span>Edit</span>
                    </a>
                    <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>
                    <a href="/central-purchase/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                        <span>Detail</span>
                    </a>
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->make(true);
    }
}
