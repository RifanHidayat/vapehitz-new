<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\PurchaseTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralPurchaseController extends Controller
{
    private function clearThousandFormat($number)
    {
        return str_replace(".", "", $number);
    }
    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }


    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_purchase_product", $permission)) {
            return view("dashboard.index");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_purchase_product", $permission)) {
            return view("dashboard.index");
        }
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
        
        $shipingCost=$this->clearThousandFormat($request->shipping_cost);
        $payAmount=$this->clearThousandFormat($request->pay_amount);
        $netto=$this->clearThousandFormat($request->netto);
        
        $centralPurchase = new CentralPurchase;
        $centralPurchase->code = $request->code;
        $centralPurchase->date = $request->date;
        $centralPurchase->supplier_id = $request->supplier_id;
        $centralPurchase->account_id = $request->account_id;
        $centralPurchase->total = $request->total;
        $centralPurchase->shipping_cost = str_replace(".", "", $request->shipping_cost);
        $centralPurchase->discount = str_replace(".", "", $request->discount);
        $centralPurchase->netto = $netto;
        $centralPurchase->pay_amount = $payAmount;
        $centralPurchase->payment_method = $request->payment_method;
        $products = $request->selected_products;
        //account transaction
        $accountTransaction=new AccountTransaction;

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

            //Transaction account Shipping Cost
            $accountTransaction->account_id="1";
            $accountTransaction->amount=$shipingCost;
            $accountTransaction->type="in";
            $accountTransaction->note="Biaya kirim Pembelian barang dengan No. Order ".$request->code;
            $accountTransaction->date=$request->date;
        
            try{
            $accountTransaction->save();
            }catch(Exception $e){
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
                ], 500);
                              
                }
     
        if ($request->pay_amount==0){
            //Transaction account debt
            $accountTransaction->account_id="3";
            $accountTransaction->amount=$netto;
            $accountTransaction->type="in";
            $accountTransaction->note="Hutang Pembelian barang dengan No. Order ".$request->code;
            $accountTransaction->date=$request->date;
               try{
                   $accountTransaction->save();
               }catch(Exception $e){
                   return response()->json([
                       'message' => 'Internal error',
                        'code' => 500,
                       'error' => true,
                       'errors' => $e,
                       ], 500);
                             
                }
    
           }else{
            //account transaction
            $accountTransaction->account_id=$request->account_id;
            $accountTransaction->amount=$request->pay_amount;
            $accountTransaction->type="out";
            $accountTransaction->note="Pembelian barang dengan code ".$request->code;
            $accountTransaction->date=$request->date;
               try{
                   $accountTransaction->save();
               }catch(Exception $e){
                   return response()->json([
                       'message' => 'Internal error',
                        'code' => 500,
                       'error' => true,
                       'errors' => $e,
                       ], 500);
                             
            
                    }

                    $date = $request->date;
                    $amount=$this->clearThousandFormat($request->pay_amount);
                    $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
                    $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

                    $transaction = new PurchaseTransaction;
                    $transaction->code = $transactionNumber;
                    $transaction->date = $request->date;
                    $transaction->account_id = $request->account_id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->amount = $amount;
                    $transaction->payment_method = $request->payment_method;
                

                    try {
                        $transaction->save();
            
                    } catch (Exception $e) {
                        return response()->json([
                            'message' => 'Internal error',
                            'code' => 500,
                            'error' => true,
                            'table'=>'Account Transaction',
                            'errors' => $e,
                        ], 500);
                    }

                    try {
                        $transaction->centralPurchases()->attach([
                            $centralPurchase->id => [
                                'amount' => $amount,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]
                        ]);
                            // return response()->json([
                            //     'message' => 'Data has been saved',
                            //     'code' => 200,
                            //     'error' => false,
                            //     'data' => $transaction,
                            // ]);
                    } catch (Exception $e) {
                        $transaction->delete();
                        return response()->json([
                            'message' => 'Internal error',
                            'code' => 500,
                            'error' => true,
                            'errors' => $e,
                        ], 500);
                    }
           }
        

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'price' => str_replace(".", "", $item['purchase_price']),
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
                // if ($productRow == null) {
                //     continue;
                // }
               
              $purchase_price= $this->clearThousandFormat($product['purchase_price']);
               // Calculate average purchase price
               $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $purchase_price)) / ($productRow->central_stock + $product['quantity']);
               $productRow->purchase_price = round($newPrice);
               $productRow->central_stock =  $productRow->central_stock + $product['quantity'];
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
                "eeeee"=>$products,
                
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_purchase_product", $permission)) {
            return view("dashboard.index");
        }
        $centralPurchase = CentralPurchase::with(['products','supplier'])->findOrFail($id);
        $payAmount = collect($centralPurchase->purchaseTransactions)->sum('pivot.amount');
        
        $transactions = collect($centralPurchase->purchaseTransactions)->sortBy('date')->values()->all();
       // return $centralPurchase;
        
      
        return view('central-purchase.show', [
            'centralPurchase' => $centralPurchase,
            'payAmount'=>$payAmount,
            'transactions'=>$transactions,
        ]);
      
    }

    public function retur($id){
        return view ('purchase-transaction.retur');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_purchase_product", $permission)) {
            return view("dashboard.index");
        }
        $suppliers = Supplier::all();
        $accounts = Account::all();
        $centralpurchases = CentralPurchase::with(['products'])->findOrFail($id);
        $selectedProducts = collect($centralpurchases->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
            $product['stock'] = $product->pivot->stock;
            $product['purchase_price'] = $product->pivot->price;
            $product['cause'] = 'defective';
        });

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
        $centralPurchase = CentralPurchase::findOrFail($id);
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
        $centralPurchase->invoice_number=$request->invoice_number;
        $centralPurchase->invoice_date=$request->invoice_date;
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
                    'price' => str_replace(".", "", $item['purchase_price']),
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $centralPurchase->products()->detach();
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

                // rumus=(((central stok lama) * (harga lama))+ (quantity lama * harga baru) )/(stok lama * quantity) 
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $centralPurchase = CentralPurchase::findOrFail($id);
        $products = $centralPurchase->products;
        try {
            
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->central_stock = $productRow->central_stock - ($product['pivot']['quantity']) ;
                $productRow->save();
            }
        
        } catch (Exception $e) {
       
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
         
            ], 500);
        }


        try {
            $centralPurchase->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
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

        $transactions = collect($purchase->purchaseTransactions)->sortBy('date')->values()->all();
        //return $transactions;


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
            $product['return_quantity'] = 0;
            $product['cause'] = 'defective';
        });

        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $payAmountPurchase = collect($purchase->purchaseTransactions)->sum('pivot.amount');

       // return $selectedProducts;


       
       // return $selectedProducts;
        return view('central-purchase.return', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
            'payAmountPurchase'=>$payAmountPurchase
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

 
    public function datatableCentralPurchase()
    {
        $centralPurchase = CentralPurchase::with(['supplier'])->select('central_purchases.*');      
        return DataTables::eloquent($centralPurchase)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return ($row->supplier!="" ? $row->supplier->name : "");
            })
            ->addColumn('netto', function ($row) {
            
                return (number_format($row->netto)); 
            })
            ->addColumn('payAmount', function ($row) {
                $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)->sum('pivot.amount');
                return (number_format($transactions));
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
                    <a href="/central-purchase/pay/' . $row->id . '"><em class="icon fas fa-check"></em>
                        <span>Pay</span>
                    </a>
                    <a href="/central-purchase/return/' . $row->id . '"><em class="icon fas fa-undo-alt"></em>
                        <span>retur</span>
                    </a>
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->addColumn('remainingAmount', function ($row) {
                $paidOff='<div><span class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span></div>';
                $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)->sum('pivot.amount');
                // return ((($row->netto)-($transactions))==0?
                // $paidOff:number_format(($row->netto)-($transactions))); 

                return number_format(($row->netto)-($transactions));
            })
            ->make(true);
    }
}
