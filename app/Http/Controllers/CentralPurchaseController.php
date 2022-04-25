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
use App\Models\CentralSale;
use App\Models\Product;
use App\Models\PurchaseReceipt;
use App\Models\PurchaseReturn;
use App\Models\PurchaseTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use PDF;

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
            //   $purchase = CentralPurchase::with(['supplier', 'products','purchaseTransactions'])->findOrFail(61);
            //     $transactions = collect($purchase->purchaseTransactions)
            //         ->where('account_id', '!=', 3)
            //         ->sum('pivot.amount');
                    
            //         return $transactions;  

        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_purchase_product", $permission)) {
            return view("dashboard.index");
        }

        // $centralPurchase = CentralPurchase::with(['purchaseReturns' => function($q) {
        //     $q->with(['purchaseReturnTransactions']);
        // }])->findOrFail(47);



        $centralpurchases = CentralPurchase::all();
        $suppliers = Supplier::all();
        return view('central-purchase.index', [
            'centralpurchases' => $centralpurchases,
            'suppliers' => $suppliers,
        ]);
        // return "tes";   

    //     $centralSales = CentralPurchase::with(['supplier'])->get()
    //     ->filter(function ($sale) {
    //         $totalPayment = collect($sale->centralPurchaseTransactions)->where('payment_method', '!=', 'hutang')->sum('amount');
    //         $sale['total_payment'] = $totalPayment;

    //         $currentDate = date('Y-m-d');
    //         $invoiceDate = date('Y-m-d', strtotime($sale->date));

    //         $diffDays = Carbon::parse($currentDate)->diffInDays($invoiceDate);

    //         $dueGroup = '0-30';

    //         if ($diffDays <= 30) {
    //             $dueGroup = '0-30';
    //         } else if ($diffDays > 30 && $diffDays <= 60) {
    //             $dueGroup = '31-60';
    //         } else if ($diffDays > 60 && $diffDays <= 90) {
    //             $dueGroup = '61-90';
    //         } else {
    //             $dueGroup = '90+';
    //         }

    //         $sale['due_group'] = $dueGroup;

    //         // if ($customer->name!==null){

    //         // }else{
                
    //         // }

    //         return $sale->netto > $totalPayment;
    //     })
    //     ->values()
    //     ->groupBy([function ($sale) {
    //     if ($sale->supplier!==null){
    //     return $sale->supplier->name; 
    //     }else{
    //         return 'r';
    //     }
    //     }, 'due_group'])
    //     // Start:Remove from this to show detail
    //     ->map(function ($customers, $key) {
    //         return collect($customers)->map(function ($group, $key) {
    //             return [
    //                 'total' => collect($group)->values()->sum(function ($sale) {
    //                     return $sale->netto - $sale->total_payment;
    //                 }),
    //             ];
    //         });
    //     })
    //     // End:Remove until this to show detail
    //     ->all();

    // return $centralSales;
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
        $accounts = Account::where('is_default',0)->get();
        //return $accounts;
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

       DB::beginTransaction();
        try {
        $payAmount = $this->clearThousandFormat($request->pay_amount);
        $netto = $this->clearThousandFormat($request->netto);
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
        $centralPurchase->is_paid = $request->pay_amount==0?false:true;
        $centralPurchase->created_by = Auth::id();     
        $centralPurchase->save();
       
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,

                'errors' => 'save purchase '.$e,
            ], 500);
        }
          //account transaction

            $accountTransaction = new AccountTransaction();
            $accountTransaction->date = $request->date;
            $accountTransaction->account_id = config('accounts.purchase_shipping_cost',1);
            $accountTransaction->amount = str_replace(".", "", $request->shipping_cost);
            $accountTransaction->type = "in";
            $accountTransaction->table_name="central_purchases";
            $accountTransaction->table_id=$centralPurchase->id;
            $accountTransaction->description="Biaya Pengiriman pembelian dengan No. pembelian ".$centralPurchase->code;
            
            try{
              
                if ((str_replace(".", "", $request->shipping_cost)!=0)){
                      $accountTransaction->save();

                }
            }catch(Exception $e){
                  DB::rollBack();
                 return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => ' shipping trasaction '.$e,
                ], 500);
            }
            
            $accountTransaction = new AccountTransaction();
            $accountTransaction->date = $request->date;
            $accountTransaction->account_id = config('accounts.purchase_discount',123);
            $accountTransaction->amount = str_replace(".", "", $request->discount);
            $accountTransaction->type = "in";
            $accountTransaction->table_name="central_purchases";
            $accountTransaction->table_id=$centralPurchase->id;
            $accountTransaction->description="Diskon pembelian dengan No. pembelian ".$centralPurchase->code;
            
            try{
              
                if ((str_replace(".", "", $request->discount)!=0)){
                      $accountTransaction->save();

                }
            }catch(Exception $e){
                  DB::rollBack();
                 return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }


        //return $products;
        if ($request->pay_amount == 0) {
            $date = $request->date;
            $amount = $this->clearThousandFormat($netto);
            $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            $transaction = new PurchaseTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->account_id = config('accounts.hutang',3);
            $transaction->supplier_id = $request->supplier_id;
            $transaction->payment_method = "hutang";
            $transaction->amount = $amount;
            $transaction->payment_init = 1;
            $transaction->central_purchase_id = $centralPurchase->id;
            $transaction->account_type = "in";
            $transaction->is_default = 1;

            try {
                $transaction->save();
            } catch (Exception $e) {
                  DB::rollBack();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'table' => 'Account Transaction',
                    'errors' => 'pruchase transaction '.$e,
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
                  DB::rollBack();
                $transaction->delete();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            //account transaction

            $accountTransaction = new AccountTransaction();
            $accountTransaction->date = $request->date;
            $accountTransaction->account_id = config('accounts.hutang',3);
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->table_name="central_purchases";
            $accountTransaction->table_id=$centralPurchase->id;
             $accountTransaction->description="Hutang Pembelian dengan No. pembelian ".$centralPurchase->code;
            
            try{
                $accountTransaction->save();
            }catch(Exception $e){
                  DB::rollBack();
                 return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => 'transaction hutang '.$e,
                ], 500);
            }

        } else {


            $date = $request->date;
            $amount = $this->clearThousandFormat($request->pay_amount);
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
            $transaction->account_type = "out";

            try {
                $transaction->save();
            } catch (Exception $e) {
                  DB::rollBack();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'table' => 'Account Transaction',
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
                  DB::rollBack();
                $transaction->delete();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            $accountTransaction = new AccountTransaction();
            $accountTransaction->date = $request->date;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->table_name="central_purchases";
            $accountTransaction->table_id=$centralPurchase->id;
             $accountTransaction->description="Pembayaran Pembelian dengan No. Transaksi".$transactionNumber;
            try{
                $accountTransaction->save();
            }catch(Exception $e){
                  DB::rollBack();
                 return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => 'pembayaran pembelian '.$e,
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
              DB::rollBack();
            $centralPurchase->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => 'update produk '.$e,
            ], 500);
        }

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                    $purchase_price = $this->clearThousandFormat($productRow->purchase_price);
                  $amount_product = $this->clearThousandFormat($product['amount_product']);
                 
                // // Calculate average purchase price
                // $newPrice = (($productRow->central_stock * $productRow->purchase_price) + ($product['quantity'] * $purchase_price)) / ($productRow->central_stock + $product['quantity']);
                
                $A1=(($purchase_price) *($productRow->central_stock + $productRow->retail_stock + $productRow->studio_stock));
                $A2=($amount_product + $A1);
                $A3=(($A2) /(($productRow->central_stock +$productRow->retail_stock + $productRow->studio_stock+$product['quantity'] + $product['free'])));
                
                // $newPrice=((((($purchase_price) * ($productRow->central_stock +$productRow->retail_stock + $productRow->studio_stock) )/2) + $purchase_price) / ($productRow->central_stock +$productRow->retail_stock + $productRow->studio_stock+$product['quantity'] + $product['free']));
                 $productRow->purchase_price = round($A3);
                
                
                $productRow->save();
            }
              DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
            ]);
        } catch (Exception $e) {
             DB::rollBack();
            $centralPurchase->products()->detach();
            $centralPurchase->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                "eeeee" => $products,

                'errors' => 'update produk 2'.$e,
            ], 500);
        }
    }

    public function receipt($id)
    {
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        // $purchaseReceipt=PurchaseReceipt::with('products')->find("14");
        $purchaseReceipt = PurchaseReceipt::with('products')->where('central_purchase_id', '=', $id)->get();


        $accounts = Account::where('is_default',0)->get();


        // return $selectedProducts;
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
        $payAmountPurchase = collect($purchase->purchaseTransactions)->where('is_default', 0)->sum('pivot.amount');


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
            $availableFreeQuantity = $product->pivot->free - $product['free'];


            $product['return_quantity'] = $availableQuantity;
            $product['remaining_free'] = $availableFreeQuantity;
            $product['initial_quantity'] = 0;
            $product['initial_free'] = 0;
            $product['cause'] = 'defective';
            $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
        })->sortBy('finish')->values()->all();



        return view('central-purchase.receipt', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
            'purchase_receipt' => $purchaseReceipt,
            'payAmountPurchase' => $payAmountPurchase
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
        $centralPurchase = CentralPurchase::with(['products', 'supplier'])->findOrFail($id);
        $payAmount = collect($centralPurchase->purchaseTransactions)->where('is_default', 0)->sum('pivot.amount');

        $transactions = collect($centralPurchase->purchaseTransactions)->where('is_default', 0)->sortBy('date')->values()->all();
        return view('central-purchase.show', [
            'centralPurchase' => $centralPurchase,
            'payAmount' => $payAmount,
            'transactions' => $transactions,
        ]);
    }


    public function retur($id)
    {
        return view('purchase-transaction.retur');
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
        // $centralPurchase = CentralPurchase::with('purchaseTransactions')->find($id);
        // $purchaseTransaction=collect($centralPurchase->purchaseTransactions)
        // ->where('payment_init',1)->first();

        // return $purchaseTransaction['id'];

        $suppliers = Supplier::all();
        $accounts = Account::where('is_default',0)->get();
        $centralpurchases = CentralPurchase::with(['products'])->findOrFail($id);
        $selectedProducts = collect($centralpurchases->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
            $product['free'] = $product->pivot->free;
            $product['stock'] = $product->pivot->stock;
            $product['purchase_price'] = $product->pivot->price;
            $product['amount_product'] = $product->pivot->price * $product->pivot->quantity;
        });
        //return $selectedProducts;



        return view('central-purchase.edit', [
            'central_purchases' => $centralpurchases,
            'suppliers' => $suppliers,
            'accounts' => $accounts,
            'selectedProducts' => $selectedProducts
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
        
         DB::beginTransaction();
        $amount = $this->clearThousandFormat($request->netto);
        $pay_amount = $this->clearThousandFormat($request->pay_amount);

        $centralPurchase = CentralPurchase::findOrFail($id);
        $centralPurchase->is_paid = $pay_amount>0?true:false;
        $centralPurchase->pay_amount = $request->pay_amount;
        $centralPurchase->code = $request->code;
        $centralPurchase->date = $request->date;
        $centralPurchase->supplier_id = $request->supplier_id;
        $centralPurchase->account_id = $request->account_id;
        $centralPurchase->total = $request->total;
        $centralPurchase->shipping_cost = str_replace(".", "", $request->shipping_cost);
        $centralPurchase->discount = str_replace(".", "", $request->discount);
        $centralPurchase->netto = $request->netto;
        $centralPurchase->payment_method = $request->payment_method;
        $centralPurchase->invoice_number = $request->invoice_number;
        $centralPurchase->invoice_date = $request->invoice_date;
        $products = $request->selected_products;
        
      

        try {
            $centralPurchase->save();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

            $accountTransaction = AccountTransaction::where('table_name','=','central_purchases')
         ->where('table_id','=',$id)
         ->where('account_id',1)
         ->first();
       
        
            
            try{
              
                if ((str_replace(".", "", $request->shipping_cost)!=0)){
                   if ($accountTransaction!=null){
                           $accountTransaction->date = $request->date;
            
            $accountTransaction->amount = str_replace(".", "", $request->shipping_cost);
          
            
          
            $accountTransaction->description="Biaya Pengiriman pembelian dengan No. pembelian ".$centralPurchase->code;
                          $accountTransaction->save();
                   }

                }
            }catch(Exception $e){
                DB::rollBack();
                 return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

      
 

       
        $centralPurchase = CentralPurchase::with('purchaseTransactions')->find($id);
        $purchaseTransactions=collect($centralPurchase->purchaseTransactions)
        ->where('payment_init',1)->first();
        $transaction = PurchaseTransaction::findOrFail($purchaseTransactions['id']);
         
        
        $accountTransaction = AccountTransaction::where('table_name','=','central_purchases')
         ->where('table_id','=',$id)
         ->where('account_id','!=',config('accounts.purchase_shipping_cost',1))
         ->first();

           

        
        if ($request->pay_amount == 0) {
       

        $transaction->date = $request->date;
        $transaction->account_id = config('accounts.hutang',3);
        $transaction->supplier_id = $request->supplier_id;
        $transaction->payment_method = "hutang";
        $transaction->amount = $amount;
        $transaction->payment_init = 1;
        $transaction->central_purchase_id = $centralPurchase->id;
        $transaction->account_type = "in";
        $transaction->is_default = 1;
         
        if ($accountTransaction==null){
        $accountTransaction = new AccountTransaction;
        $accountTransaction->date = $request->date;
        $accountTransaction->account_id = config('accounts.hutang',3);
        $accountTransaction->amount = $amount;
        $accountTransaction->type = "in";
        $accountTransaction->description="Hutang Pembelian dengan No.".$transaction['code'];

        }else{
        $accountTransaction->date = $request->date;
        $accountTransaction->account_id = config('accounts.hutang',3);
        $accountTransaction->amount = $amount;
        $accountTransaction->type = "in";
        $accountTransaction->description="Hutang Pembelian dengan No.".$transaction['code'];

        }
       



         try {
            $accountTransaction->save();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        }else{
   
        
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->supplier_id = $request->supplier_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->payment_init = 1;
            $transaction->central_purchase_id = $centralPurchase->id;
            $transaction->account_type = "out";
            $transaction->is_default = 0;

           

            if ($accountTransaction==null){
        $accountTransaction = new AccountTransaction;
        $accountTransaction->date = $request->date;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->description="Pembayaran pembelian  dengan No.".$transaction['code'];

        }else{
         $accountTransaction->date = $request->date;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->description="Pembayaran pembelian  dengan No.".$transaction['code'];

        }

                try {
            $accountTransaction->save();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
           
        }

       

        try {
            $transaction->save();
        } catch (Exception $e) {
            DB::rollBack();
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
           
        } catch (Exception $e) {
            DB::rollBack();
            $centralPurchase->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
       

        try {
            $transaction->centralPurchases()->detach();
        } catch (Exception $e) {
            DB::rollBack();
               $transaction->delete();
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
            DB::rollBack();
            $centralPurchase->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
       
        }
        
        try {
            $transaction->centralPurchases()->attach([
                $centralPurchase->id=> [
                    'amount' => $amount,
                    'payment_init' => 1,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $transaction->delete();
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
            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
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
      //   DB::beginTransaction();

               //return $id;

        //  $accountTransaction = AccountTransaction::where('table_name','=','central_purchases','and','table_id','=',$id)->get();

           DB::beginTransaction();

        $centralPurchase=CentralPurchase::with(['purchaseTransactions'])->findOrFail($id);
        $purchaseReturns=PurchaseReturn::with('purchaseReturnTransactions')->where('central_purchase_id',$id)->get();

        
    $accountTransactions = AccountTransaction::where('table_name','=','central_purchases')
            ->where('table_id','=',$id)
            ->get();
         
        try{
            foreach ($accountTransactions as $accountTransaction){
                if ($accountTransaction!=null){
            $accountTransaction->delete();
            }
            
            }
            
          
            

        }catch(Exception $e){
             DB::rollBack();
              return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e.'',

            ], 500);

        }

        try{
        foreach ($centralPurchase->purchaseTransactions as $purchaseTransaction){
            $accountTransactions = AccountTransaction::where('table_name','=','purchase_transactions')
            ->where('table_id','=',$purchaseTransaction->id)
            ->get();

            foreach($accountTransactions as $accountTransaction){
            if ($accountTransaction!=null){
                    $accountTransaction->delete();
            }

            }

            $purchaseTransaction->delete();
        }

        }catch(Exception $e){
             DB::rollBack();
             return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e.'',

            ], 500);

        }

        try{
        foreach ($purchaseReturns as $purchaseReturn){
            foreach ($purchaseReturn->purchaseReturnTransactions as $purchaseReturnTransaction){
            $accountTransactions = AccountTransaction::where('table_name','=','purchase_return_transactions')
            ->where('table_id','=',$purchaseReturnTransaction->id)
            ->get();

           if ($purchaseReturnTransaction!=null){
                $purchaseReturnTransaction->delete();
           }
            //     if ($accountTransaction!=null){
            //         $accountTransaction->delete();
            // }

             foreach($accountTransactions as $accountTransaction){
            if ($accountTransaction!=null){
                    $accountTransaction->delete();
            }
                
            }

            }
           
          
      

        }

        }catch(Exception $e){
             DB::rollBack();
             return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e.'',

            ], 500);

        }


    
        $centralPurchase = CentralPurchase::findOrFail($id);
        


        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);
     

        //product
        $purchaseReceiptProducts = PurchaseReceipt::with(['products'])->where("central_purchase_id", $id)
            ->get()
            ->flatMap(function ($receipt) {
                return $receipt->products;
            })->groupBy('id')
            ->map(function ($product, $key) {
                $receivedQuantity = collect($product)->sum(function ($item) {
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
//return $purchaseReceiptProducts;
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
       //  return $saleReturnProducts;
        $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts, $purchaseReceiptProducts) {
            $saleReturn = collect($saleReturnProducts)->where('id', $product['id'])->first();
            $receipt = collect($purchaseReceiptProducts)->where('id', $product['id'])->first();
            $product['returned_quantity'] = 0;
            if ($saleReturn !== null) {
                $product['returned_quantity'] = $saleReturn['returned_quantity'];
            }
            else {
                $product['returned_quantity'] = 0;
            }
            if ($receipt !== null) {
                $product['received_quantity'] = $receipt['received_quantity'];
                 $product['free'] = $receipt['free'];
            } else {
                $product['received_quantity'] = 0;
            }
            $availableQuantity = $product['received_quantity'] - $product['returned_quantity'];
           
            $product['return_quantity'] = $availableQuantity;
            $product['initial_quantity'] = 0;
            $product['cause'] = 'defective';
            $product['finish'] = $product['returned_quantity'] >= $product['received_quantity'] ? 1 : 0;
        })->sortBy('finish')->values()->all();

//return $selectedProducts;
        //purchase Return
        try {
            PurchaseReturn::query()->where('central_purchase_id', $id)->delete();
        } catch (Exception $e) {
             DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,

            ], 500);
        }
        //  purchase Return Transactions
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
        try {
            // $purchase->purchaseTransactions()->detach();
            PurchaseReceipt::query()->where('central_purchase_id', $id)->delete();
        } catch (Exception $e) {
             DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,

            ], 500);
        }

        //    // product purchase Receipt
        //      try{
        //         // $purchase->purchaseTransactions()->detach();
        //         DB::table('product_purchase_receipt')->where('central_purchase_id', $id)->delete();
        //     }catch (Exception $e) {
        //         return response()->json([
        //             'message' => 'Internal error',
        //             'code' => 500,
        //             'error' => true,
        //             'errors' => $e,

        //         ], 500);
        //     }


        try {
            $centralPurchase->purchaseTransactions()->detach();
        } catch (Exception $e) {
             DB::rollBack();
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
                    $productRow->central_stock - ($product['return_quantity'] + $product['free']);
                $productRow->save();
            }
        } catch (Exception $e) {
             DB::rollBack();

            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,

            ], 500);
        }
     

        try {
            $centralPurchase->delete();
            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
            ]);
        } catch (Exception $e) {
             DB::rollBack();
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
        $accounts = Account::where('is_default',0)->get();

        $transactions = collect($purchase->purchaseTransactions)->where('is_default', 0)->sortBy('date')->values()->all();
        //return $transactions;
        return view('central-purchase.pay', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'transactions' => $transactions,
        ]);
    }

    public function print($id)
    {
        // return view('central-sale.print');
     
        $purchase=CentralPurchase::with(['products','supplier'])->findOrFail($id);
   
        $payAmount = collect($purchase->purchaseTransactions)->where('is_default', 0)->sum('pivot.amount');
      

        //return $purchase;
        

        $data = [
            
            'purchase'=>$purchase,
            "pay_amount"=>$payAmount,
        ];


        
        $pdf = PDF::loadView('central-purchase.reports', $data);
        return $pdf->stream($purchase->code . '.pdf');
    }

    public function return($id)
    {
        $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($id);

        $accounts = Account::where('is_default',0)->get();

        $payAmountPurchase = collect($purchase->purchaseTransactions)->where('is_default', 0)->sum('pivot.amount');
        $purchaseReceiptProducts = PurchaseReceipt::with(['products'])->where("central_purchase_id", $id)
            ->get()
            ->flatMap(function ($receipt) {
                return $receipt->products;
            })->groupBy('id')
            ->map(function ($product, $key) {
                $receivedQuantity = collect($product)->sum(function ($item) {
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
            });

         

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
        
        $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts, $purchaseReceiptProducts) {
            $saleReturn = collect($saleReturnProducts)->where('id', $product['id'])->first();
            $receipt = collect($purchaseReceiptProducts)->where('id', $product['id'])->first();
            $product['returned_quantity'] = 0;
            if ($saleReturn !== null) {
                $product['returned_quantity'] = $saleReturn['returned_quantity'];
            }
            if ($receipt !== null) {
                $product['received_quantity'] = $receipt['received_quantity'];
            } else {
                $product['received_quantity'] = 0;
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


       // return $selectedProducts;

        return view('central-purchase.return', [
            'purchase' => $purchase,
            'accounts' => $accounts,
            'selected_products' => $selectedProducts,
            'payAmountPurchase' => $payAmountPurchase
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
         
       // return "tes";

        // $startDate = $request->query('start_date');
        // $endDate = $request->query('end_date');
        // $reportType = $request->query('report_type');

        // $startDate = $request->start_date;
        // $endDate = $request->end_date;
        // $supplier = $request->supplier;
        // $shipment = $request->shipment;
        // $status = $request->status;
        // $sortBy = $request->sort_by;
        // $sortIn = $request->sort_in;

        // $query = Product::whereHas('centralPurchases', function (Builder $query) use ($startDate, $endDate) {
        //     $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        // })->with(['productCategory', 'productSubcategory', 'centralPurchases' => function ($query) use ($supplier, $shipment, $status) {
        //     $query->with(['supplier', 'createdBy']);
        //     if ($supplier !== '' && $supplier !== null) {
        //         $query->where('supplier_id', $supplier);
        //     }

        //     // if ($shipment !== '' && $shipment !== null) {
        //     //     $query->where('shipment_id', $shipment);
        //     // }

        //     // if ($status !== '' && $status !== null) {
        //     //     $query->where('status', $status);
        //     // }
        // }]);

        // $purchases = $query->get();
        // return $purchases;

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
                return ($row->supplier != "" ? $row->supplier->name : "");
            })
            ->addColumn('netto', function ($row) {
                return (number_format($row->netto));
            })
            ->addColumn('payAmount', function ($row) {
                $purchase = CentralPurchase::with(['supplier', 'products','purchaseTransactions'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)
                        ->where('is_default', '!=', 1)
                    ->sum('pivot.amount');  
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
                     <a href="/central-purchase/print/' . $row->id . '" target="_blank"><em class="icon fas fa-print"></em>
                    <span>Cetak</span>
                </a>

                </ul>
            </div>
            </div>';
                return $button;
            })

            ->addColumn('remainingAmount', function ($row) {
                $paidOff = '<div><span class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span></div>';
                 $purchase = CentralPurchase::with(['supplier', 'products','purchaseTransactions'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)
                     ->where('is_default', '!=', 1)
                    ->sum('pivot.amount');
                $remainingAmount=$row->netto-$transactions;
                return number_format($remainingAmount );
            })
            ->make(true);
    }
}
