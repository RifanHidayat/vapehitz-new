<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseTransactionController extends Controller
{
    
    public function index()
    {
        return view('purchase-transaction.index');
    }

    public function create(Request $request)
    {
    }

    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }
    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $date = $request->date;
        $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
        $purchaseId = $request->purchase_id;
        $amount = $this->clearThousandFormat($request->amount);

       

 

        //update amount central purchase
        try{
            $centralPurchase = CentralPurchase::find($purchaseId);
            $centralPurchase->pay_amount=$centralPurchase->pay_amount + (str_replace(".", "", $request->amount)) ;
            $centralPurchase->save();

        }catch(Exception $e){
               DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'table'=>'Central Purchase',
                'errors' => $e,
            ], 500);
        }

       
            $date = $request->date;
            $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
            $purchaseId = $request->purchase_id;
            $amount = $this->clearThousandFormat($request->amount);
            
            $transaction = new PurchaseTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->supplier_id = $request->supplier_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;
            $transaction->account_id = $request->account_id;
            $transaction->account_type = "out";

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
        $accountTransaction=new AccountTransaction;

          $accountTransaction->date = $request->date;
          $accountTransaction->account_id = "3";
          $accountTransaction->amount = $amount;
          $accountTransaction->type = "out";
          $accountTransaction->description="Pembayaran Hutang dengan No.".$transactionNumber;
          $accountTransaction->note = $request->note;
          $accountTransaction->table_name="purchase_transactions";
          $accountTransaction->table_id=$transaction->id;
          

            try{
                $accountTransaction->save();

            }catch(Exception $e){
                   DB::rollBack();
                return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'table'=>'Central Purchase',
                'errors' => $e,
               
            ], 500);

            }
            $accountTransaction=new AccountTransaction;

          $accountTransaction->date = $request->date;
          $accountTransaction->account_id = $request->account_id;
          $accountTransaction->amount = $amount;
          $accountTransaction->type = "out";
          $accountTransaction->description="Pembayaran Pembelian dengan No.".$transactionNumber;
          $accountTransaction->note = $request->note;
          $accountTransaction->table_name="purchase_transactions";
          $accountTransaction->table_id=$transaction->id;
          

            try{
                $accountTransaction->save();

            }catch(Exception $e){
                   DB::rollBack();
                return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'table'=>'Central Purchase',
                'errors' => $e,
               
            ], 500);

            }



        // $keyedQuotations = collect($quotations)->mapWithKeys(function ($item) {
        //     return [
        //         $item['id'] => [
        //             'estimation_id' => $item['selected_estimation'],
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ];
        // })->all();

        try {
            $transaction->centralPurchases()->attach([
                $purchaseId => [
                    'amount' => $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
             DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => null,
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

     
    }

    public function show($id)
    {
        $purchaseTransaction = PurchaseTransaction::with(['supplier','account', 'centralPurchases.products'])->findOrFail($id);
       
        return view('purchase-transaction.show', [
            'purchaseTransaction' => $purchaseTransaction,
        ]);
    }
    public function destroy($id)
    {
          DB::beginTransaction();
   
        $PurchaseTransaction = PurchaseTransaction::findOrFail($id);
         $accountTransactions = AccountTransaction::where('table_name','=','purchase_transactions')->where('table_id',$id)->get();
       try{
            foreach ($accountTransactions as $accountTransaction) {
               
                $accountTransaction->delete();
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
       try{
        DB::table('central_purchase_purchase_transaction')->where('purchase_transaction_id', $id)->delete();
       }catch(Exception $e){
              DB::rollBack();
             return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);

       }
        try {
            $PurchaseTransaction->delete();
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
                'errors' => $e,
            ], 500);
        }
   
    }

  

    public function datatablePurchaseTransaction()
    {
        $PurchaseTransaction = PurchaseTransaction::with(['supplier','account', 'centralPurchases'])->select('purchase_transactions.*')->where('is_default',0);
        return DataTables::eloquent($PurchaseTransaction)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return ($row->supplier ? $row->supplier->name : "");
            })
            ->addColumn('account', function ($row) {
                return ($row->payment_method." (".$row->account->name.")");
            })
            ->addColumn('amount', function ($row) {
                return (number_format($row->amount));
            })
            ->addColumn('action', function ($row) {
                if (($row->payment_init==0)){
                    $delete=' <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>';

                }else{
                    $delete='';

                }
                $button = '
            <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                  
                  '.$delete.'
                    <a href="/purchase-transaction/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
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
