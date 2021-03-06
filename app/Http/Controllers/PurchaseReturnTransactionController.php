<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PurchaseReturnTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('purchase-return-transaction.index');
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
    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }
    private function clearThousandFormat($number)
    {
        return str_replace(".", "", $number);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = $request->date;
        $transactionsByCurrentDateCount = PurchaseReturnTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'PRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        $purchaseId = $request->purchase_id;
        $amount = $this->clearThousandFormat($request->amount);
        $debt = $this->clearThousandFormat($request->debt);

        $purchaseReturnTransaction = new PurchaseReturnTransaction;
        $purchaseReturnTransaction ->code = $transactionNumber;
        $purchaseReturnTransaction ->date = $request->date;
        $purchaseReturnTransaction ->account_id = $request->account_id;
        $purchaseReturnTransaction->supplier_id = $request->supplier_id;
        $purchaseReturnTransaction->amount = $amount;
        $purchaseReturnTransaction ->payment_method = $request->payment_method;
        $purchaseReturnTransaction ->note = $request->note;
        $purchaseReturnTransaction ->purchase_return_id = $request->purchase_return_id;
        $purchaseReturnTransaction ->account_type = "in";

        DB::beginTransaction();
         //user account
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


         $date = $request->date;
         $transactionsByCurrentDateCount = PurchaseReturnTransaction::query()->where('date', $date)->get()->count();
         $transactionNumber = 'PRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
 
         $purchaseId = $request->purchase_id;
         $amount = $this->clearThousandFormat($request->amount);
         $debt = $this->clearThousandFormat($request->debt);
 
    

              $accountTransaction=new AccountTransaction;
              $accountTransaction->date = $request->date;
              $accountTransaction->account_id = config('accounts.piutang',34);
              $accountTransaction->amount = $amount;
              $accountTransaction->type = "out";
              $accountTransaction->note = $request->note;
               $accountTransaction->description="Pembayaran Piutang dengan No. Transaksi ".$transactionNumber;
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


             $accountTransaction=new AccountTransaction;
              $accountTransaction->date = $request->date;
              $accountTransaction->account_id =$request->account_id;
              $accountTransaction->amount = $amount;
              $accountTransaction->type = "in";
              $accountTransaction->note = $request->note;
              $accountTransaction->description="Pembayaran Retur dengan No. Transaksi ".$transactionNumber;
               $accountTransaction->table_name="purchase_return_transactions";
                $accountTransaction->table_id=$purchaseReturnTransaction->id;


                try{
                $accountTransaction->save();
                DB::commit();
                 return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => [],
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



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $purchaseReturnTransaction = PurchaseReturnTransaction::with(['supplier','account'])->findOrFail($id);

        $purchaseReturn=PurchaseReturn::with(['products','centralPurchase'])->findOrFail($purchaseReturnTransaction->purchase_return_id);
      //  return $purchaseReturn;
       

        
        return view('purchase-return-transaction.show', [
            'purchaseReturnTransaction' => $purchaseReturnTransaction,
            'purchaseReturn'=>$purchaseReturn
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
        $purchaseReturnTransaction= PurchaseReturnTransaction::findOrFail($id);

        try {
            $purchaseReturnTransaction->delete();
        
        } catch (Exception $e) {
              DB::rollBack();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

           $accountTransactions = AccountTransaction::where('table_name','=','purchase_return_transactions')
           ->where('table_id','=',$id)->get();
     
         try{
            //return $accountTransaction;
             foreach ($accountTransactions as $accountTransaction){
                 if ($accountTransaction!=null){
                      $accountTransaction->delete();

                 }
                
             }

                DB::commit();
                 return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => [],
            ]);
             
         }catch(Exception $e){
               DB::rollBack();
              return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => '1'.$e,

            ], 500);

         }
        
        //
    }

    public function datatablePurchaseReturnTransactions()
    {
        $purchaseReturnTransaction = PurchaseReturnTransaction::with(['account','supplier'])->select('purchase_return_transactions.*')->where('is_default',0);
        return DataTables::eloquent($purchaseReturnTransaction)
            ->addIndexColumn()
            ->addColumn('supplier_name', function ($row) {
                return ($row->supplier ? $row->supplier->name : "");
            })
            ->addColumn('account', function ($row) {
                return ($row->payment_method.'('.$row->account->name.')');
            })
            ->addColumn('amount', function ($row) {
                return (number_format($row->amount));
            })
            
            ->addColumn('action', function ($row) {

               
                if ($row->payment_init!=1){
                     $delete='  <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
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

                    <a href="/purchase-return-transaction/show/' . $row->id . '" ><em class="icon fas fa-eye"></em>
                    <span>Detail</span>
                    </a>
                 
                  
                </ul>
            </div>
            </div>';
                return $button;
            }
            )
            ->make(true);
    }
}
