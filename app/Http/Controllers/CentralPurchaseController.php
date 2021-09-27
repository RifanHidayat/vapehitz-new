<?php

namespace App\Http\Controllers;

use App\Exports\CentralPurchaseByProductDetailExport;
use App\Exports\CentralPurchaseByProductSummaryExport;
use App\Exports\CentralPurchaseBySupplierDetailExport;
use App\Exports\CentralPurchaseBySupplierSummaryExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReturn;
use App\Models\PurchaseTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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
        $centralPurchase->created_by = Auth::id();
      //  return $products;
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

        //return $products;
 
        if ($request->pay_amount==0){

            $date = $request->date;
            $amount=$this->clearThousandFormat($netto);
            $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            $transaction = new PurchaseTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->account_id = "3";
            $transaction->supplier_id = $request->supplier_id;
            $transaction->payment_method = "hutang";
            $transaction->amount = $amount;
            $transaction->payment_init = 1;
            $transaction->central_purchase_id = $centralPurchase->id;
              
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
                    'payment_init' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                    } catch (Exception $e) {
                        $transaction->delete();
                        return response()->json([
                            'message' => 'Internal error',
                            'code' => 500,
                            'error' => true,
                            'errors' => $e,
                        ], 500);
                    }
           }else{
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
            $transaction->payment_init = 1;
            $transaction->central_purchase_id = $centralPurchase->id;
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
                    'payment_init' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

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
                    'free' => $item['free'],
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
              $purchase_price= $this->clearThousandFormat($product['purchase_price']);
               // Calculate average purchase price
               $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $purchase_price)) / ($productRow->central_stock + $product['quantity']);
               $productRow->purchase_price = round($newPrice);
            //    $productRow->central_stock =  $productRow->central_stock + $product['quantity'];
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

    public function receipt($id){
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        // $purchaseReceipt=PurchaseReceipt::with('products')->find("14");
        $purchaseReceipt=PurchaseReceipt::with('products')->where('central_purchase_id','=',$id)->get();
        
        
        $accounts = Account::all();

        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['return_quantity'] = 0;
        //     $product['free'] = 0;
        //     $product['cause'] = 'defective';
        // });

       // return $selectedProducts;
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $payAmountPurchase = collect($purchase->purchaseTransactions)->sum('pivot.amount');

        

        //note
        //returne quantity= receipt quantity
        //return quantity=remaining
        $saleReturnProducts = PurchaseReceipt::with(['products'])
        ->where('central_purchase_id', $id)
        ->get()
        ->flatMap(function ($saleReturn) {
            return $saleReturn->products;
        })->groupBy('id')
        ->map(function ($group, $id) {
            $returnedQuantity = collect($group)->map(function ($product) {
                return $product->pivot->quantity;
            })->sum();
            $freeQuantity = collect($group)->map(function ($product) {
                return $product->pivot->free;
            })->sum();
            return [
                'id' => $id,
                'returned_quantity' => $returnedQuantity,
                'free' => $freeQuantity,
            ];
        })
        ->all();
       // return $saleReturnProducts;

    $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts) {
        $saleReturn = collect($saleReturnProducts)->where('id', $product->id)->first();
        $product['returned_quantity'] = 0;
        $product['free'] = 0;
        if ($saleReturn !== null) {
            $product['returned_quantity'] = $saleReturn['returned_quantity'];
            $product['free'] = $saleReturn['free'];
        }
        $availableQuantity = $product->pivot->quantity - $product['returned_quantity'];
        $availableFreeQuantity=$product->pivot->free - $product['free'];
        

        $product['return_quantity'] = $availableQuantity;
        $product['remaining_free']=$availableFreeQuantity;
        $product['initial_quantity'] = 0;
        $product['initial_free'] = 0;
        $product['cause'] = 'defective';
        $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
    })->sortBy('finish')->values()->all();

  

        return view('central-purchase.receipt', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
            'purchase_receipt'=>$purchaseReceipt,
            'payAmountPurchase'=>$payAmountPurchase
        ]);

    

   

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
            $product['free'] = $product->pivot->free;
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

        $purchaseTransaction=PurchaseTransaction::find($id);

       
       

       


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
                    'free' => $item['free'],
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
            // foreach ($products as $product) {
            //     $productRow = Product::find($product['id']);
            //     if ($productRow == null) {
            //         continue;
            //     }

            //     // Calculate average purchase price
            //     $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $product['purchase_price'])) / ($productRow->central_stock + $product['quantity']);
            //     $productRow->purchase_price = round($newPrice);
            //     //$productRow->central_stock = $productRow->central_stock + $product['quantity'];
            //     $productRow->save();

            //     // rumus=(((central stok lama) * (harga lama))+ (quantity lama * harga baru) )/(stok lama * quantity) 
            // }
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
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $centralPurchase = CentralPurchase::findOrFail($id);

        $purchaseReceiptProducts = PurchaseReceipt::with(['products'])->where("central_purchase_id",$id)
        ->get()
        ->flatMap(function($receipt) {
            return $receipt->products;
        })->groupBy('id')
        ->map(function($product, $key) {
            $receivedQuantity = collect($product)->sum(function($item) {
                return $item->pivot->quantity;
            });
            $freeQuantity = collect($product)->map(function ($product) {
                return $product->pivot->free;
            })->sum();

            return [
                'id' => $key,
                'received_quantity' => $receivedQuantity,
                'free' => $freeQuantity,
            ];
        })->values()->all();
        
        $saleReturnProducts = PurchaseReturn::with(['products'])
        ->where('central_purchase_id', $id)
        ->get()
        ->flatMap(function ($saleReturn) {
            return $saleReturn->products;
        })->groupBy('id')
        ->map(function ($group, $id) {
            $returnedQuantity = collect($group)->map(function ($product) {
                return $product->pivot->quantity;
            })->sum();
            $freeQuantity = collect($group)->map(function ($product) {
                return $product->pivot->free;
            })->sum();
            return [
                'id' => $id,
                'returned_quantity' => $returnedQuantity,
                'free' => $freeQuantity,
            ];
        })
        ->all();
       
        // return $purchase->products;
        // return $saleReturnProducts;
        $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts,$purchaseReceiptProducts) {
        $saleReturn = collect($saleReturnProducts)->where('id', $product['id'])->first();
        $receipt=collect($purchaseReceiptProducts)->where('id', $product['id'])->first();
        $product['returned_quantity'] = 0;
        if ($saleReturn !== null) {
            $product['returned_quantity'] = $saleReturn['returned_quantity'];
        }
        if ($receipt!==null){
            $product['received_quantity']=$receipt['received_quantity'];
            
        }else{
            $product['received_quantity']=0;

        }
        $availableQuantity = $product['received_quantity'] - $product['returned_quantity'];
        $product['free'] = $receipt['free'];
        $product['return_quantity'] = $availableQuantity;
        $product['initial_quantity'] = 0;
        $product['cause'] = 'defective';
        $product['finish'] = $product['returned_quantity'] >= $product['received_quantity'] ? 1 : 0;
        })->sortBy('finish')->values()->all();
       

        //purchase Return
        try{
            DB::table('purchase_returns')->where('central_purchase_id', $id)->delete();
        }catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
         
            ], 500);
        }
         //purchase Return Transactions
        //  try{
        //     DB::table('purchase_return_transactions')->where('central_purchase_id', $id)->delete();
        // }catch (Exception $e) {
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }

     
         //purchase Receipt
         try{
            // $purchase->purchaseTransactions()->detach();
            DB::table('purchase_receipts')->where('central_purchase_id', $id)->delete();
        }catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
         
            ], 500);

        }

        //product purchase Receipt
        //  try{
        //     // $purchase->purchaseTransactions()->detach();
        //     DB::table('product_purchase_receipt')->where('central_purchase_id', $id)->delete();
        // }catch (Exception $e) {
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
         
        //     ], 500);
        // }
       

        try{
            $centralPurchase->purchaseTransactions()->detach();

        }catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
         
            ], 500);
        }

    

        try {    
            foreach ($selectedProducts as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->central_stock = 
                    $productRow->central_stock - ($product['return_quantity']-$product['free'])
                     ;
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


        $purchaseReceiptProducts = PurchaseReceipt::with(['products'])->where("central_purchase_id",$id)
        ->get()
        ->flatMap(function($receipt) {
            return $receipt->products;
        })->groupBy('id')
        ->map(function($product, $key) {
            $receivedQuantity = collect($product)->sum(function($item) {
                return $item->pivot->quantity;
            });
            $freeQuantity = collect($product)->map(function ($product) {
                return $product->pivot->free;
            })->sum();

            return [
                'id' => $key,
                'received_quantity' => $receivedQuantity,
                'free' => $freeQuantity,
            ];
            // $product = collect($product)->values()->all();
        })->values()->all();
        
        $saleReturnProducts = PurchaseReturn::with(['products'])
        ->where('central_purchase_id', $id)
        ->get()
        ->flatMap(function ($saleReturn) {
            return $saleReturn->products;
        })->groupBy('id')
        ->map(function ($group, $id) {
            $returnedQuantity = collect($group)->map(function ($product) {
                return $product->pivot->quantity;
            })->sum();
            $freeQuantity = collect($group)->map(function ($product) {
                return $product->pivot->free;
            })->sum();
            return [
                'id' => $id,
                'returned_quantity' => $returnedQuantity,
                'free' => $freeQuantity,
            ];
        })
        ->all();
       
        // return $purchase->products;
        // return $saleReturnProducts;
        $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts,$purchaseReceiptProducts) {
        $saleReturn = collect($saleReturnProducts)->where('id', $product['id'])->first();
        $receipt=collect($purchaseReceiptProducts)->where('id', $product['id'])->first();
        $product['returned_quantity'] = 0;
        if ($saleReturn !== null) {
            $product['returned_quantity'] = $saleReturn['returned_quantity'];
        }
        if ($receipt!==null){
            $product['received_quantity']=$receipt['received_quantity'];
            
        }else{
            $product['received_quantity']=0;

        }
       
        //$availableQuantity = $product->pivot->quantity - $product['returned_quantity'];
        $availableQuantity = $product['received_quantity'] - $product['returned_quantity'];
        
        $product['free'] = $receipt['free'];
        $product['return_quantity'] = $availableQuantity;
        $product['initial_quantity'] = 0;
        $product['cause'] = 'defective';
        // $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
        $product['finish'] = $product['returned_quantity'] >= $product['received_quantity'] ? 1 : 0;
        })->sortBy('finish')->values()->all();

       
        
        $payAmountPurchase = collect($purchase->purchaseTransactions)->sum('pivot.amount');
        return view('central-purchase.return', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
            'payAmountPurchase'=>$payAmountPurchase
        ]);
    }
    public function reportBySupplier(Request $request)
    {

        // return CentralSale::with(['products', 'customer'])->get()->groupBy(function ($item, $key) {
        //     return $item->customer->name;
        // })->all();

        // $query = CentralPurchase::with(['products' => function ($q) {
        //     $q->with(['productCategory', 'productSubcategory']);
        // }, 'supplier']);

        // $purchases = $query->get()->map(function ($purchase, $key) {
        //     $totalForSupplier = 0;
        //     return [
        //         'supplier' => $purchase->supplier->name,
        //         'total' => $totalForSupplier
        //     ];
        // })->all();
        //---------------
        // $query = Supplier::with(['centralPurchases' => function ($query) {
        //     $query->with(['supplier', 'products']);
        // }]);

        // $purchases = $query->get()->map(function ($supplier, $key) {
        //     $totalForSupplier = collect($supplier->centralPurchases)->sum(function ($purchase) {
        //         return collect($purchase->products)->sum(function ($product) {
        //             return $product->pivot->quantity * $product->pivot->price;
        //         });
        //     });
        //     return [
        //         'supplier' => $supplier->name,
        //         'total' => $totalForSupplier,
        //     ];
        // })->all();

        // return $purchases;

        //-----------------------------------------------------

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new CentralPurchaseBySupplierDetailExport($request->all()), 'Central Purchases By Supplier Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
            return Excel::download(new CentralPurchaseBySupplierSummaryExport($request->all()), 'Central Purchases By Supplier Summary ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
    }

    public function reportByProduct(Request $request)
    {

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new CentralPurchaseByProductDetailExport($request->all()), 'Central Purchases By Product Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
            return Excel::download(new CentralPurchaseByProductSummaryExport($request->all()), 'Central Purchases By Product Summary ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */


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
        //return $products;

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
                    <a href="/central-purchase/receipt/' . $row->id . '"><em class="icon fas fa-receipt"></em>
                        <span>Penerimaan Barang</span>
                    </a>
                    <a href="/central-purchase/pay/' . $row->id . '"><em class="icon fas fa-credit-card"></em>
                        <span>Bayar</span>
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



