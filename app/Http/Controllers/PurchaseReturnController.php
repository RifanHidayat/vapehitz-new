<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnController extends Controller
{
    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }
    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('purchase-return.index');
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
        
        $maxid = DB::table('purchase_returns')->max('id');
        $code = "PR/VH/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);

        $total_return_amount = $this->clearThousandFormat($request->total_return_amount);
        $remaining_pay = $this->clearThousandFormat($request->remaining_pay);
        $purchaseReturn = new PurchaseReturn();
        $purchaseReturn->code=$code;
        $purchaseReturn->date=$request->date;
        $purchaseReturn->account_id=$request->account_id;
        $purchaseReturn->supplier_id=$request->supplier_id;
        $purchaseReturn->payment_method=$request->payment_method;
        $purchaseReturn->quantity=$request->total_return_quantity;
        $purchaseReturn->amount=$request->total_return_amount;
        $purchaseReturn->note=$request->note;
        $purchaseReturn->central_purchase_id=$request->purchase_id;
        $products=$request->products;
      
   
        DB::beginTransaction();
        //save purchase return
        try {
            $purchaseReturn->save();
        } catch (Exception $e) {
              DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,

                'errors' => $e,
            ], 500);
        }

        //update amount central purchase
        $centralPurchase = CentralPurchase::find($request->purchase_id);
        if ($request->payment_method=='hutang'){
            if ($total_return_amount>$remaining_pay){
           

                // pay purchase return transaction
                $date = $request->date;
                $transactionsByCurrentDateCount = PurchaseReturnTransaction::query()->where('date', $date)->get()->count();
                $transactionNumber = 'PRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);               

                $purchaseReturnTransaction = new PurchaseReturnTransaction;
                $purchaseReturnTransaction ->code = $transactionNumber;
                $purchaseReturnTransaction ->date = $request->date;
                $purchaseReturnTransaction ->account_id = $request->account_id;
                $purchaseReturnTransaction ->supplier_id = $request->supplier_id;
                $purchaseReturnTransaction ->amount =$remaining_pay ;
                $purchaseReturnTransaction ->payment_method = $request->payment_method;
                $purchaseReturnTransaction ->note = $request->note;
                $purchaseReturnTransaction ->purchase_return_id = $purchaseReturn->id;
                $purchaseReturnTransaction ->account_type = "in";
                $purchaseReturnTransaction->payment_init=1;

             
               
                try{
                   $purchaseReturnTransaction->save();
               } catch (Exception $e) {
                   return response()->json([
                       'message' => 'Internal error',
                       'code' => 500,
                       'error' => true,
                       'errors' => $e,
                   ], 500);
               }

                $accountTransaction=new AccountTransaction;
                $accountTransaction->date = $request->date;
                $accountTransaction->account_id = config('accounts.piutang',34);
                $accountTransaction->amount = $total_return_amount-$remaining_pay;
                $accountTransaction->type = "in";
                $accountTransaction->note = $request->note;
                $accountTransaction->description="Piutang supplier dengan No. Transaksi ".$transactionNumber;
                $accountTransaction->table_name="purchase_return_transactions";
                $accountTransaction->table_id=$purchaseReturnTransaction->id;

                 try{
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

                
                 //pay purchase transaction
                 $date = $request->date;
                 $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
                 $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                 $purchaseTransaction = new PurchaseTransaction;
                 $purchaseTransaction->code = $transactionNumber;
                 $purchaseTransaction->date = $request->date;
                 $purchaseTransaction->account_id = $request->account_id;
                 $purchaseTransaction->supplier_id = $request->supplier_id;
                 $purchaseTransaction->amount = $remaining_pay;
                 $purchaseTransaction->payment_method = $request->payment_method;
                 $purchaseTransaction->note = $request->note;
                 $purchaseTransaction ->account_type = "out";
                 $purchaseTransaction->payment_init=2;
                 $purchaseTransaction->central_purchase_id=$purchaseReturn->id;
                 try{
                     $purchaseTransaction->save();
                 } catch (Exception $e) {
                       DB::rollBack();
                     return response()->json([
                         'message' => 'Internal error',
                         'code' => 500,
                         'error' => true,
                         'errors' => $e,
                     ], 500);
                 }
                $accountTransaction=new AccountTransaction;
                $accountTransaction->date = $request->date;
                $accountTransaction->account_id = config('accounts.hutang',3);
                $accountTransaction->amount = $remaining_pay;
                $accountTransaction->type = "out";
                $accountTransaction->note = $request->note;
                $accountTransaction->description="Pembayaran retur dengan No. Transaksi ".$transactionNumber;
                $accountTransaction->table_name="purchase_return_transactions";
              $accountTransaction->table_id=$purchaseReturnTransaction->id;

                 try{
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


                
                try {
                    $purchaseTransaction->centralPurchases()->attach([
                        $request->purchase_id => [
                            'amount' => $remaining_pay,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);
                } catch (Exception $e) {
                      DB::rollBack();
                    $purchaseTransaction->delete();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }
            } else {
                try {
                    $centralPurchase->pay_amount = $centralPurchase->pay_amount + (str_replace(".", "", $request->total_return_amount));
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

               

                //pay purchase transaction
                $date = $request->date;
                $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
                $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $purchaseTransaction = new PurchaseTransaction;
                $purchaseTransaction->code = $transactionNumber;
                $purchaseTransaction->date = $request->date;
                $purchaseTransaction->account_id = $request->account_id;
                $purchaseTransaction->supplier_id = $request->supplier_id;
                $purchaseTransaction->amount = $total_return_amount;
                $purchaseTransaction->payment_method = $request->payment_method;
                $purchaseTransaction->note = $request->note;
                $purchaseTransaction->account_type = "out";
                $purchaseTransaction->payment_init=2;
                $purchaseTransaction->central_purchase_id=$purchaseReturn->id;
                try {
                    $purchaseTransaction->save();
                } catch (Exception $e) {
                      DB::rollBack();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }
                $accountTransaction=new AccountTransaction;
                $accountTransaction->date = $request->date;
                $accountTransaction->account_id = config('accounts.hutang',3);
                $accountTransaction->amount =$total_return_amount;
                $accountTransaction->type = "out";
                $accountTransaction->note = $request->note;
                $accountTransaction->description="Pembayaran retur  dengan No. Transaksi ".$transactionNumber;
                $accountTransaction->table_name="purchase_return_transactions";
                $accountTransaction->table_id=$purchaseTransaction->id;

                 try{
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
                try {
                    $purchaseTransaction->centralPurchases()->attach([
                        $request->purchase_id => [
                            'amount' => $total_return_amount,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);
                } catch (Exception $e) {
                      DB::rollBack();
                    $purchaseTransaction->delete();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }

              
                  

                  // pay purchase return transaction
                  $date = $request->date;
                  $transactionsByCurrentDateCount = PurchaseReturnTransaction::query()->where('date', $date)->get()->count();
                  $transactionNumber = 'PRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);               
                  $purchaseReturnTransaction = new PurchaseReturnTransaction;
                  $purchaseReturnTransaction ->code = $transactionNumber;
                  $purchaseReturnTransaction ->date = $request->date;
                  $purchaseReturnTransaction ->account_id = $request->account_id;
                  $purchaseReturnTransaction ->supplier_id = $request->supplier_id;
                  $purchaseReturnTransaction->amount=$total_return_amount ;                                                                                         $purchaseReturnTransaction ->payment_method = $request->payment_method;
                  $purchaseReturnTransaction ->note = $request->note;
                  $purchaseReturnTransaction ->purchase_return_id = $purchaseReturn->id;
                  $purchaseReturnTransaction ->account_type="in";
                  $purchaseReturnTransaction->payment_init=1;

 
                  try{
                     $purchaseReturnTransaction->save();
                 }catch(Exception $e){
                       DB::rollBack();
                     return response()->json([
                         'message' => 'Internal error',
                          'code' => 500,
                         'error' => true,
                         'errors' => $e,
                         ], 500);                               
                  }
               
            }

        }else{       
          // pay purchase return transaction
                $date = $request->date;
                $transactionsByCurrentDateCount = PurchaseReturnTransaction::query()->where('date', $date)->get()->count();
                $transactionNumber = 'PRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $purchaseReturnTransaction = new PurchaseReturnTransaction;
                $purchaseReturnTransaction ->code = $transactionNumber;
                $purchaseReturnTransaction ->date = $request->date;
                $purchaseReturnTransaction ->account_id = $request->account_id;
                $purchaseReturnTransaction ->supplier_id = $request->supplier_id;
                $purchaseReturnTransaction ->amount =$total_return_amount ;
                $purchaseReturnTransaction ->payment_method = $request->payment_method;
                $purchaseReturnTransaction ->note = $request->note;
                $purchaseReturnTransaction ->purchase_return_id = $purchaseReturn->id;
                $purchaseReturnTransaction ->account_type = "in";
                 $purchaseReturnTransaction->payment_init=1;
                try{
                    $purchaseReturnTransaction->save();
                } catch (Exception $e) {
                      DB::rollBack();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }
                $accountTransaction=new AccountTransaction;
                $accountTransaction->date = $request->date;
                $accountTransaction->account_id = $request->account_id;
                $accountTransaction->amount =$total_return_amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = $request->note;
                $accountTransaction->description="Pembayaran retur  dengan No. Transaksi ".$transactionNumber;
                $accountTransaction->table_name="purchase_return_transactions";
                $accountTransaction->table_id=$purchaseReturnTransaction->id;

                 try{
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
    

              //update stock central
              try {
                foreach ($products as $product) {  
                if ($product['initial_quantity']<=0){
                    continue;
                }
                DB::table('central_purchase_product')
                ->where('product_id', $product['id'])
                ->where('central_purchase_id', $product['pivot']['central_purchase_id'])
                ->update(['return_quantity' =>$product['pivot']['return_quantity']+$product['initial_quantity']]);
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
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->central_stock = $productRow->central_stock - ($product['initial_quantity']) ;
                $productRow->save();
                //update purchase product
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

    //save produk purchase return
    $keyedProducts = collect($products)->filter(function ($item){
        return ($item['initial_quantity']!=0) ;
    })->mapWithKeys(function ($item) {
        return [
            $item['id'] => [
                'quantity' => $item['initial_quantity'],
                'cause'=>$item['cause'],
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]
        ];
    })->all();

    try {
        $purchaseReturn->products()->attach($keyedProducts);
         DB::commit();
          return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
            ]);
    } catch (Exception $e) {
          DB::rollBack();
        $centralPurchase->delete();
        return response()->json([
            'message' => 'Internal error',
            'code' => 500,
            'error' => true,
            'errors' => ''.$e,
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
        $payAmount = 0;
        $accounts = Account::all();
        $purchaseReturn = PurchaseReturn::with(['centralPurchase', 'supplier', 'account', 'products'])->findOrFail($id);

        //Purchase Transaction
        if ($purchaseReturn->centralPurchase->id != null) {
            $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($purchaseReturn->centralPurchase->id);
            $payAmount = collect($purchase->purchaseTransactions)->where('is_default',0)->sum('pivot.amount');
        }

        $transactions = collect($purchaseReturn->purchaseReturnTransactions)->where('is_default',0)->sortBy('date')->values()->all();
       //  return $transactions;

    
  
        return view('purchase-return.show', [
            'purchaseReturn' => $purchaseReturn,
            'accounts' => $accounts,
            'payAmount'=>$payAmount,
            'transactions'=>$transactions
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

        DB::beginTransaction();

        $PurchaseReturn = PurchaseReturn::with(['purchaseReturnTransactions','products'])->findOrFail($id);

       try{
            foreach ($PurchaseReturn->purchaseReturnTransactions as $purchaseReturnTransaction){

            $accountTransactions = AccountTransaction::where('table_name','=','purchase_return_transactions')
            ->where('table_id','=',$purchaseReturnTransaction->id)
            ->get();
           
           foreach( $accountTransactions as $accountTransaction  ){
                if ($accountTransaction!=null){
                 $accountTransaction->delete();

            }
           }

            if ($purchaseReturnTransaction!=null){
                 $purchaseReturnTransaction->delete();

            }
      

        }

       }catch(Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
                "e"=>'ww'+$e
            ], 500);

       }
       $purchaseTransaction=PurchaseTransaction::where('central_purchase_id',$id)
       ->where('payment_init',2)->first();

       try{
        if ($purchaseTransaction!=null){
            $purchaseTransaction->delete();
          }

       }catch(Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
                "e"=>'ww'+$e
            ], 500);

       }

        //return $PurchaseReturn;
    
        $products = $PurchaseReturn->products;
        try {
            $PurchaseReturn->delete();
        } catch (Exception $e) {
                  DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
                "e"=>''+$e
            ], 500);
        }

        try{
            $PurchaseReturn->purchaseReturnTransactions()->delete();

        }catch (Exception $e) {
                  DB::rollBack();
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
                $productRow->central_stock = $productRow->central_stock + ($product['pivot']['quantity']) ;
                $productRow->save();
            }
        } catch (Exception $e) {
                  DB::rollBack();
       
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => '5'.$e,
         
            ], 500);
        }
        try {
            DB::table("product_purchase_return")->where('purchase_return_id', $id)->delete();
            // $purchaseReturnTransaction->delete();
             DB::commit();
            return response()->json([
                'message' => 'Data has been deleted',
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
                'errors' => '1'.$e,
            ], 500);
        }

    }

    public function pay($id)
    {
        $payAmount = 0;
        $accounts = Account::where('is_default',0)->get();
        $purchaseReturn = PurchaseReturn::with(['centralPurchase', 'supplier', 'account'])->findOrFail($id);

        //Purchase Transaction
        if ($purchaseReturn->centralPurchase->id != null) {
            $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($purchaseReturn->centralPurchase->id);
            $payAmount = collect($purchase->purchaseTransactions)->sum('pivot.amount');
        }

        $transactions = collect($purchaseReturn->purchaseReturnTransactions)->where('account_id','!=',config('accounts.piutang',34))->sortBy('date')->values()->all();
   
        return view('purchase-return.pay', [
            'purchaseReturn' => $purchaseReturn,
            'accounts' => $accounts,
            'payAmount' => $payAmount,
            'transactions' => $transactions

        ]);
        //
    }

    public function datatablePurchaseReturn()
    {

        $PurchaseReturn = PurchaseReturn::with(['supplier', 'centralPurchase'])->select('purchase_returns.*');
        //return $row->centralPurchase->code;
        return DataTables::eloquent($PurchaseReturn)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return ($row->supplier ? $row->supplier->name : "");
            })
            ->addColumn('central_purchase_code', function ($row) {
                return ($row->centralPurchase->code);
            })
            ->addColumn('payAmount', function ($row) {
                $purchase = PurchaseReturn::with(['supplier'])->findOrFail($row->id);
                $payAmount = collect($purchase->purchaseReturnTransactions)
                ->where('account_id','!=',2)
                ->sum('amount');

                return number_format($payAmount);
            })
            ->addColumn('amount', function ($row) {

                return number_format($row->amount);
            })
            ->addColumn('remainingAmount', function ($row) {
                $purchase = PurchaseReturn::with(['supplier'])->findOrFail($row->id);
                $payAmount = collect($purchase->purchaseReturnTransactions)
                ->where('account_id','!=',2)
                ->sum('amount');
                
                return number_format($row->amount - $payAmount);
            })

            // button delete
          
            ->addColumn('action', function ($row) {
                $button = '
            <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                <span>Delete</span>
                </a>
                   
                 
                    <a href="/purchase-return/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                    <span>Detail</span>
                 
                </a>
                
                   
                    <a href="/purchase-return/pay/' . $row->id . '"><em class="icon fas fa-credit-card"></em>
                    <span>bayar</span>
                 
                </a>
                   
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->make(true);
    }
}
