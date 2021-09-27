<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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
        $date = $request->date;
        $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
        $purchaseId = $request->purchase_id;
        $amount = $this->clearThousandFormat($request->amount);

 
        try{
            $transaction = new PurchaseTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->supplier_id = $request->supplier_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;
            $transaction->account_id = "3";
            $transaction->save();
        }catch(Exception $e){
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'table'=>'Central Purchase',
                'errors' => $e,
               
            ], 500);
        }
        
        //update amount central purchase
        try{
            $centralPurchase = CentralPurchase::find($purchaseId);
            $centralPurchase->pay_amount=$centralPurchase->pay_amount + (str_replace(".", "", $request->amount)) ;
            $centralPurchase->save();

        }catch(Exception $e){
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'table'=>'Central Purchase',
                'errors' => $e,
            ], 500);
        }

       

        try {
            $transaction = new PurchaseTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->supplier_id = $request->supplier_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;
            $transaction->account_id = $request->account_id;
            $transaction->save();

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
               
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
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $transaction,
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

    public function show($id)
    {
        $purchaseTransaction = PurchaseTransaction::with(['supplier','account', 'centralPurchases.products'])->findOrFail($id);
       
        return view('purchase-transaction.show', [
            'purchaseTransaction' => $purchaseTransaction,
        ]);
    }
    public function destroy($id)
    {
        // return response()->json([
        //     'message' => 'Data has been deleted',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => null,
        // ]);
        $PurchaseTransaction = PurchaseTransaction::findOrFail($id);

        try {
            $PurchaseTransaction->delete();
            return response()->json([
                'message' => 'Data has been deleted',
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

  

    public function datatablePurchaseTransaction()
    {
        $PurchaseTransaction = PurchaseTransaction::with(['supplier','account', 'centralPurchases'])->select('purchase_transactions.*')->where('account_id','!=','3');
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
                $button = '
            <div class="drodown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                  
                    <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>
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
