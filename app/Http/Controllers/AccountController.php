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
use GuzzleHttp\Psr7\Request as Psr7Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
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
    
        $accounts = collect(Account::all())->each(Function($account){
            

            //purchase transactions
             $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($account['id']);
             
    
             //purchase rertun transactions      
             $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
             ->findOrfail($account['id']);
             
     
             //in out transaction
             $InOutTransactionAccount=Account::with('accountTransactions')->find($account['id']);
             
            
             //supping cost
             $centralPurchases=CentralPurchase::where('shipping_cost','>',0,)->get();
        //sale
            $studioSaleTransactions=Account::with('studioSaleTransactions')->find($account['id']);
            $retailSaleTransactions=Account::with('retailSaleTransactions')->find($account['id']);
            $centralSaleTransactions=Account::with('centralSaleTransactions')->find($account['id']);
             
             $transactionMerge = 
             (($purchaseTransactions->purchaseTransactions)
             ->merge($purchaseReturnTransactions->purchaseReturnTransactions)
             ->merge($centralPurchases)
             ->merge($InOutTransactionAccount->accountTransactions)
             ->merge($centralSaleTransactions->centralSaleTransactions)
             ->merge($retailSaleTransactions->retailSaleTransactions)
             ->merge($studioSaleTransactions->studioSaleTransactions));
    
    
            $accountTransactions=collect($transactionMerge)->sortBy('date')->all();
            $cashIn=collect($accountTransactions)->where('account_type','==','in')->sum('amount');
            $cashOut=collect($accountTransactions)->where('account_type','==','out')->sum('amount'); 
            $account['balance']=$cashIn+$account['init_balance']-$cashOut;
                
            });
            
            //return $accounts;
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
    public function show(Request $request,$id)
    { 
        $startDate=$request->query('start_date');
        $endtDate=$request->query('end_date');

        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_account_finance", $permission)) {
            return redirect("/dashboard");
        }
        $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);  
        $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
        ->findOrfail($id);
        $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
        $account=collect(Account::where('id','=',$id)->get())->each(function($account){
            $account['account_type'] = 'in';
            $account['note'] = 'Saldo Awal';
            $account['amount'] = $account['init_balance'];
        });
        $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
        ($centralPurchase){
            $centralPurchase['account_type'] = 'in';
            $centralPurchases['account_id']="1";
            $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
            $centralPurchase['description'] = $centralPurchase['note'];
            $centralPurchase['amount']=$centralPurchase['shipping_cost'];
        });

        //sale
        $studioSaleTransactions=Account::with('studioSaleTransactions')->find($id);
        $retailSaleTransactions=Account::with('retailSaleTransactions')->find($id);
        $centralSaleTransactions=Account::with('centralSaleTransactions')->find($id);
            

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
        ->merge(collect($centralSaleTransactions->centralSaleTransactions)
        ->each(function($centralSaleTransaction){
                $centralSaleTransaction['description']=
                    "Transaksi Penjualan pusat dengan No. Transaksi ".$centralSaleTransaction['code'];
                $centralSaleTransaction['amount']=abs($centralSaleTransaction['amount']);
             }))  
        ->merge(collect($studioSaleTransactions->studioSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                    "Transaksi Penjualan studio dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
             }))   
        ->merge(collect($retailSaleTransactions->retailSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                "Transaksi Penjualan retail dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
        }))   
        ->merge($InOutTransactionAccount->accountTransactions))
        : $transactionMerge = $centralPurchases;

       
        if (($startDate=='') && ($endtDate=='')){
            $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        }else{
            $accountTransactions=collect($transactionMerge)
            ->whereBetween('date',[$startDate,$endtDate])
            ->sortBy('date')
            ->values()
            ->all();
        }

        // if (($startDate!=null) && ($endtDate!=null)){
        //     $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        // }else{
        //     $accountTransactions=collect($transactionMerge)
        //     ->where('date','>=',$startDate,'AND','date','<=',$endtDate)
        //     ->sortBy('date')
        //     ->values()
        //     ->all();
        // }
        // $accountTransactions=collect($transactionMerge)
        //     ->where('date','>=',$startDate,'AND','date','<=',$endtDate)
        //     ->sortBy('date')
        //     ->values()
        //     ->all();
        $cashIn=collect($accountTransactions)->where('account_type','==','in')->sum('amount');
        $cashOut=collect($accountTransactions)->where('account_type','==','out')->sum('amount'); 
        $account = Account::with(['accountTransactions'])->findOrFail($id);
        
        return view('account.show', [
            "account_id" => $id,
            "cash_in" => $cashIn,
            "cash_out" => $cashOut,
            "balance" => $cashIn - $cashOut,
            "account" => $account,
            "start_date"=>$startDate,
            "end_date"=>$endtDate
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
    public function reports(request $request,$id)
    {
        $startDate=$request->query('start_date');
        $endtDate=$request->query('end_date');
        
        $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);  
        $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
        ->findOrfail($id);
        $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
        $account=collect(Account::where('id','=',$id)->get())->each(function($account){
            $account['account_type'] = 'in';
            $account['note'] = 'Saldo Awal';
            $account['amount'] = $account['init_balance'];
        });
        $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
        ($centralPurchase){
            $centralPurchase['account_type'] = 'in';
            $centralPurchases['account_id']="1";
            $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
            $centralPurchase['description'] = $centralPurchase['note'];
            $centralPurchase['amount']=$centralPurchase['shipping_cost'];
        });

        //sale
        $studioSaleTransactions=Account::with('studioSaleTransactions')->find($id);
        $retailSaleTransactions=Account::with('retailSaleTransactions')->find($id);
        $centralSaleTransactions=Account::with('centralSaleTransactions')->find($id);
            

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
        ->merge(collect($centralSaleTransactions->centralSaleTransactions)
        ->each(function($centralSaleTransaction){
                $centralSaleTransaction['description']=
                    "Transaksi Penjualan pusat dengan No. Transaksi ".$centralSaleTransaction['code'];
                $centralSaleTransaction['amount']=abs($centralSaleTransaction['amount']);
             }))  
        ->merge(collect($studioSaleTransactions->studioSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                    "Transaksi Penjualan studio dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
             }))   
        ->merge(collect($retailSaleTransactions->retailSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                "Transaksi Penjualan retail dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
        }))   
        ->merge($InOutTransactionAccount->accountTransactions))

        : $transactionMerge = $centralPurchases;
    

        // $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        // $accountTransactions=collect($transactionMerge)
        // ->where('date','>=',$startDate,'AND','date','<=',$endtDate)
        // ->sortBy('date')
        // ->values()
        // ->all();
        if (($startDate=='') && ($endtDate=='')){
            $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        }else{
            $accountTransactions=collect($transactionMerge)
            ->whereBetween('date',[$startDate,$endtDate])
            ->sortBy('date')
            ->values()
            ->all();
        }
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

    public function export(Request $request, $id){
        $startDate=$request->query('start_date');
        $endtDate=$request->query('end_date');
        $account=Account::with('purchaseTransactions')->findOrfail($id);
        return Excel::download(new TransactionAccountExport($id,$startDate,$endtDate), 'Detail akun ' . $account->name . '( ' .$account->number. ' )' . "" . '.xlsx');
    }

    // private function makeLink($id, $code)
    // {
    //     return '<a ' . '' 
    // }


    public function datatableAccountTransactions(Request $request,$id)
    {

        $startDate=$request->query('start_date');
        $endtDate=$request->query('end_date');
        //return $startDate;
        
        $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);  
        $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
        ->findOrfail($id);
        $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
        $account=collect(Account::where('id','=',$id)->get())->each(function($account){
            $account['note'] = '';
            $account['description'] = 'Saldo Awal';
            $account['account_type'] = 'in';
            $account['amount'] = $account['init_balance'];
            $account['note'] = '';
        });
        $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
        ($centralPurchase){
            $centralPurchase['account_type'] = 'in';
            $centralPurchases['account_id']="1";
            $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
            $centralPurchase['description'] = $centralPurchase['note'];
            $centralPurchase['amount']=$centralPurchase['shipping_cost'];
        });

        //sale
        $studioSaleTransactions=Account::with('studioSaleTransactions')->find($id);
        $retailSaleTransactions=Account::with('retailSaleTransactions')->find($id);
        $centralSaleTransactions=Account::with('centralSaleTransactions')->find($id);
            

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
        ->merge(collect($centralSaleTransactions->centralSaleTransactions)
        ->each(function($centralSaleTransaction){
                $centralSaleTransaction['description']=
                    "Transaksi Penjualan pusat dengan No. Transaksi ".$centralSaleTransaction['code'];
                $centralSaleTransaction['amount']=abs($centralSaleTransaction['amount']);
             }))  
        ->merge(collect($studioSaleTransactions->studioSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                    "Transaksi Penjualan studio dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
             }))   
        ->merge(collect($retailSaleTransactions->retailSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                "Transaksi Penjualan retail dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
        }))   
        ->merge($InOutTransactionAccount->accountTransactions))

        : $transactionMerge = $centralPurchases;

        if (($startDate=='') && ($endtDate=='')){
            $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        }else{
            $accountTransactions=collect($transactionMerge)
            ->whereBetween('date',[$startDate,$endtDate])
            ->sortBy('date')
            ->values()
            ->all();
        }
        // $accountTransactions=collect($transactionMerge)
        // ->whereBetween('date',[$startDate,$endtDate])
        //     ->sortBy('date')
        //     ->values()
        //     ->all();
        
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
        return Datatables::of($accountTransactionCollection->sortBy('date')->values()->all())
       
        ->rawColumns(['description'])
        ->make(true);

    }


    public function datatableAccounts()
    {
        

        $accounts = Account::select('accounts.*');
        //return $row->centralPurchase->code;
        return DataTables::eloquent($accounts)
            ->addIndexColumn()
            ->addColumn('balance', function ($row) {
                $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($row->id);  
                $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
                ->findOrfail($row->id);
                $InOutTransactionAccount=Account::with('accountTransactions')->find($row->id);
                $account=collect(Account::where('id','=',$row->id)->get())->each(function($account){
                    $account['account_type'] = 'in';
                    $account['note'] = 'Saldo Awal';
                    $account['amount'] = $account['init_balance'];
                });
                $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
                ($centralPurchase){
                    $centralPurchase['account_type'] = 'in';
                    $centralPurchases['account_id']="1";
                    $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
                    $centralPurchase['description'] = $centralPurchase['note'];
                    $centralPurchase['amount']=$centralPurchase['shipping_cost'];
                });
        
                //sale
                $studioSaleTransactions=Account::with('studioSaleTransactions')->find($row->id);
                $retailSaleTransactions=Account::with('retailSaleTransactions')->find($row->id);
                $centralSaleTransactions=Account::with('centralSaleTransactions')->find($row->id);
                    
        
                //checked shpping account
                $row->id!="1"
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
                ->merge(collect($centralSaleTransactions->centralSaleTransactions)
                ->each(function($centralSaleTransaction){
                        $centralSaleTransaction['description']=
                            "Transaksi Penjualan pusat dengan No. Transaksi ".$centralSaleTransaction['code'];
                        $centralSaleTransaction['amount']=abs($centralSaleTransaction['amount']);
                     }))  
                ->merge(collect($studioSaleTransactions->studioSaleTransactions)
                ->each(function($studioSaleTransaction){
                        $studioSaleTransaction['description']=
                            "Transaksi Penjualan studio dengan No. Transaksi ".$studioSaleTransaction['code'];
                        $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
                     }))   
                ->merge(collect($retailSaleTransactions->retailSaleTransactions)
                ->each(function($studioSaleTransaction){
                        $studioSaleTransaction['description']=
                        "Transaksi Penjualan retail dengan No. Transaksi ".$studioSaleTransaction['code'];
                        $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
                }))   
                ->merge($InOutTransactionAccount->accountTransactions))
        
                : $transactionMerge = $centralPurchases;
            
        
                $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

       
         

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
