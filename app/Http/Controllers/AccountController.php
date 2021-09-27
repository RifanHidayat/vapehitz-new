<?php

namespace App\Http\Controllers;

use App\Exports\CentralPurchaseBySupplierDetailExport;
use App\Exports\TransactionAccountExport;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

use function GuzzleHttp\Promise\each;


class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::all();
        return view('account.index', [
            'accounts' => $accounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accounts = Account::All();
        return view('account.create', [
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
        $accounts = new Account;
        $accounts->number = $request->number;
        $accounts->name = $request->name;
        $accounts->date = $request->date;
        $accounts->init_balance = str_replace(".", "", $request->init_balance);
        $accounts->type = $request->type;
        try {
            $accounts->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $accounts,
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
         //purchase transactions
         $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);

         //purchase rertun transactions      
         $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
         ->findOrfail($id);
 
         //in out transaction
         $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
         
         //opening balance
         $account=collect(Account::where('id','=',$id)->get())->each(function($account){
             $account['account_type'] = 'in';
             $account['Description'] = 'Saldo Awal';
             $account['amount'] = $account['init_balance'];
         });
         
         //supping cost
         $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
         ($centralPurchase){
             $centralPurchase['account_type'] = 'in';
             $centralPurchases['account_id']="1";
             $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
             $centralPurchase['note'] = $centralPurchase['note'];
             $centralPurchase['amount']=$centralPurchase['shipping_cost'];
         });
         
         
        
 
         //checked shpping account
         $id!="1"
         ? $transactionMerge = 
         ($account
         ->merge(collect($purchaseTransactions->purchaseTransactions)
            ->each(function($purchaseTransaction){
                $purchaseTransaction['description']=
                "Pembayaran retur dengan No. Transaksi ".$purchaseTransaction['code'];
            }))
            
         ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
            ->each(function($purchaseReturnTransaction){
                $purchaseReturnTransaction['description']=
                    "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
             }))
         ->merge($InOutTransactionAccount->accountTransactions))
 
         : $transactionMerge = $centralPurchases;

        $accountTransactions=collect($transactionMerge)->sortBy('date')->all();
       
        $cashIn=collect($accountTransactions)->where('account_type','==','in')->sum('amount');
        $cashOut=collect($accountTransactions)->where('account_type','==','out')->sum('amount'); 
        $account = Account::with(['accountTransactions'])->findOrFail($id);

        return view('account.show', [
            "account_id" => $id,
            "cash_in" => $cashIn,
            "cash_out" => $cashOut,
            "balance" => $cashIn - $cashOut,
            "account" => $account
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
    public function reports($id)
    {
          //purchase transactions
          $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);

          //purchase rertun transactions      
          $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
          ->findOrfail($id);
  
          //in out transaction
          $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
          
          //opening balance
          $account=collect(Account::where('id','=',$id)->get())->each(function($account){
              $account['account_type'] = 'in';
              $account['Description'] = 'Saldo Awal';
              $account['amount'] = $account['init_balance'];
          });
          
          //supping cost
          $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
          ($centralPurchase){
              $centralPurchase['account_type'] = 'in';
              $centralPurchases['account_id']="1";
              $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
              $centralPurchase['note'] = $centralPurchase['note'];
              $centralPurchase['amount']=$centralPurchase['shipping_cost'];
          });
          

          //checked shpping account
          $id!="1"
  
          ? $transactionMerge = 
          ($account
          ->merge(collect($purchaseTransactions->purchaseTransactions)
             ->each(function($purchaseTransaction){
                 $purchaseTransaction['description']=
                 "Pembayaran retur dengan No. Transaksi".$purchaseTransaction['code'];
             }))
             
          ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
             ->each(function($purchaseReturnTransaction){
                 $purchaseReturnTransaction['description']=
                     "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
              }))
          ->merge($InOutTransactionAccount->accountTransactions))
  
          : $transactionMerge = $centralPurchases;
         

        $accountTransactions=collect($transactionMerge)->sortBy('date')->all();
        $cashIn=collect($accountTransactions)->where('account_type','==','in')->sum('amount');
        $cashOut=collect($accountTransactions)->where('account_type','==','out')->sum('amount');
    
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' =>
            'A4', 'defaultPageNumStyle' => '1',
            'margin_right' => '5',
            'margin_left' => '5',
            'margin_bottom' => '10',
            'margin_top' => '6',
          ]);
          $data = view('account.reports',[
              'transactions'=>$accountTransactions,
              'inTotal'=>$cashIn,
              "outTotal"=>$cashOut,
              "account"=>$account[0]
              ]);

         // $mpdf->Image('http://127.0.0.1:8004/images/vapehitz-logo2.jpeg', 0, 0, 210, 297, 'jpeg', '', true, false);
          $mpdf->setFooter('{PAGENO}');
          $mpdf->WriteHTML($data);
          $mpdf->Output();
    }

    public function download(){
       try{
        $attemptToWriteObject = CentralPurchase::all();
        $attemptToWriteText = "Hi";
        Storage::put('attempt1.txt', $attemptToWriteText);

       }catch(Exception $e){
        return response()->json([
            'message' => 'Internal error',
            'code' => 500,
            'error' => true,
            'errors' => $e,
        ], 500);


       }
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
        $account = Account::find($id);
        $account->number = $request->number;
        $account->name = $request->name;
        $account->date = $request->date;
        $account->init_balance = str_replace(".", "", $request->init_balance);
        $account->type = $request->type;
        try {
            $account->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $account,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        try {
            $account->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $account,
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

    public function export($id){
        $account=Account::with('purchaseTransactions')->findOrfail($id);
        return Excel::download(new TransactionAccountExport($id), 'Detail akun ' . $account->name . '( ' .$account->number. ' )' . "" . '.xlsx');
    }


    public function datatableAccountTransactions($id)
    {
 
        //purchase transactions
        $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);

        //purchase rertun transactions      
        $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
        ->findOrfail($id);

        //in out transaction
        $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
        
        //opening balance
        $account=collect(Account::where('id','=',$id)->get())->each(function($account){
            $account['account_type'] = 'in';
            $account['note'] = 'Saldo Awal';
            $account['amount'] = $account['init_balance'];
        });
        
        //supping cost
        $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
        ($centralPurchase){
            $centralPurchase['account_type'] = 'in';
            $centralPurchases['account_id']="1";
            $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
            $centralPurchase['description'] = $centralPurchase['note'];
            $centralPurchase['amount']=$centralPurchase['shipping_cost'];
        });

        //checked shpping account
        $id!="1"

        ? $transactionMerge = 
        ($account
        ->merge(collect($purchaseTransactions->purchaseTransactions)
           ->each(function($purchaseTransaction){
               $purchaseTransaction['description']=
               "pembayaran supplier dengan No. Order".$purchaseTransaction['code'];
           }))

        ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
           ->each(function($purchaseReturnTransaction){
               $purchaseReturnTransaction['description']=
                   "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
            }))
        ->merge($InOutTransactionAccount->accountTransactions))

        : $transactionMerge = $centralPurchases;
    

        $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();
        $accountTransactionCollection = new Collection();
        $balance=0;
       // return $accountTransactions;
        for ($i = 0; $i < count($accountTransactions); $i++) {
            //balance
            $accountTransactions[$i]['account_type']=="in"?
            $balance=$balance+$accountTransactions[$i]['amount']:
            $balance=$balance-$accountTransactions[$i]['amount'];
            
            $accountTransactionCollection->push([
                'date' => $accountTransactions[$i]['date'],
                'note' => $accountTransactions[$i]['name'],
                'description' => $accountTransactions[$i]['description'],
                'in'   => 
                    $accountTransactions[$i]['account_type']=="in"?
                    number_format($accountTransactions[$i]['amount']):
                    "",
                'out' => 
                    $accountTransactions[$i]['account_type']=="out"?
                    number_format($accountTransactions[$i]['amount']):
                    "",
                'balance' => number_format($balance)

            ]);
        }
        return Datatables::of($accountTransactionCollection->sortBy('date')->values()->all())->make(true);

    }


    public function datatableAccounts()
    {


        $accounts = Account::select('accounts.*');
        //return $row->centralPurchase->code;
        return DataTables::eloquent($accounts)
            ->addIndexColumn()
            ->addColumn('balance', function ($row) {
                        //purchase transactions
          $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($row->id);

          //purchase rertun transactions      
          $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
          ->findOrfail($row->id);
  
          //in out transaction
          $InOutTransactionAccount=Account::with('accountTransactions')->find($row->id);
          
          //opening balance
          $account=collect(Account::where('id','=',$row->id)->get())->each(function($account){
              $account['account_type'] = 'in';
              $account['Description'] = 'Saldo Awal';
              $account['amount'] = $account['init_balance'];
          });
          
          //supping cost
          $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
          ($centralPurchase){
              $centralPurchase['account_type'] = 'in';
              $centralPurchases['account_id']="1";
              $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
              $centralPurchase['note'] = $centralPurchase['note'];
              $centralPurchase['amount']=$centralPurchase['shipping_cost'];
          });
          

          //checked shpping account
          $row->id!="1"
  
          ? $transactionMerge = 
          ($account
          ->merge(collect($purchaseTransactions->purchaseTransactions)
             ->each(function($purchaseTransaction){
                 $purchaseTransaction['description']=
                 "Pembayaran retur dengan No. Transaksi ".$purchaseTransaction['code'];
             }))
             
          ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
             ->each(function($purchaseReturnTransaction){
                 $purchaseReturnTransaction['description']=
                     "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
              }))
          ->merge($InOutTransactionAccount->accountTransactions))
  
          : $transactionMerge = $centralPurchases;
         

        $accountTransactions=collect($transactionMerge)->sortBy('date')->all();
        $cashIn=collect($accountTransactions)->where('account_type','==','in')->sum('amount');
        $cashOut=collect($accountTransactions)->where('account_type','==','out')->sum('amount');

                return number_format($cashIn-$cashOut);
                
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
                    <a  onclick="onEditAccount('.$row.')"><em class="icon fas fa-edit"></em>
                    <span>Edit</span>
                 
                </a>
                    <a href="/account/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
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
