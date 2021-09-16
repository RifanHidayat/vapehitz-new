<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
           
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        $accountTransaaction = new AccountTransaction;
        
        $accountTransaaction->account_id = $accounts->id;
        $accountTransaaction->date = $request->date;
        $accountTransaaction->amount = str_replace(".", "", $request->init_balance);
        $accountTransaaction->type = "in";
        $accountTransaaction->note = "Saldo Awal";
        $accountTransaaction->table_name = "accounts";
        $accountTransaaction->table_id = $accounts->id;

        try {
            $accountTransaaction->save();
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
        $account = Account::with(['accountTransactions'])->findOrFail($id);
        $cashIn=collect($account['accountTransactions'])->where('type','=','in')->sum('amount');
        $cashOut=collect($account['accountTransactions'])->where('type','=','out')->sum('amount'); 
        $accountTransactions = DB::table('account_transactions')->where('account_id','=',$id)->get();
      //  return $accountTransactions;        
        return view('account.show', [
            "account_id"=>$id,
            "cash_in"=>$cashIn,
            "cash_out"=>$cashOut,
            "balance"=>$cashIn-$cashOut,
            "account"=>$account
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


    public function datatableAccountTransactions($id)
    {
        // $accountTransactions = AccountTransaction::with(['account'])->where('account_id','=',$id)->select('account_transactions.*')->get()->each(function($value) use ($balance) {     
        //     if ($value['type']=='in')
        //    {
        //     $balance=$value['amount'];
        //     $in=$value['amount'];
        //     $out="";
        //    }else{
        //     $balance=$balance-$value['amount'];
        //     $out=$value['amount'];
        //     $in="";
        //     }
        //     $value['balance']   =$balance;
        //     $value['in']   =$in;
        //     $value['out']   =$out;
        // }); 

        $accountTransactionCollection = new Collection();
        $accountTransactions = AccountTransaction::with(['account'])->where('account_id','=',$id)->select('account_transactions.*')->get();
        $balance=0;
        for ($i = 0; $i < count($accountTransactions); $i++) {
            $accountTransactionCollection->push([
                'date' => $accountTransactions[$i]['date'],
                'note' => $accountTransactions[$i]['name'],
                'type' => $accountTransactions[$i]['type'],
                'in'   => 
                    $accountTransactions[$i]['type']=="in"?
                    number_format($accountTransactions[$i]['amount']):
                    "",
                'out' => 
                    $accountTransactions[$i]['type']=="out"?
                    number_format($accountTransactions[$i]['amount']):
                    "",
                'balance' => 
                    $accountTransactions[$i]['type']=="in"?
                    $balance+=$accountTransactions[$i]['amount']:
                    $balance-=$accountTransactions[$i]['amount'],
            ]);
        }
        return Datatables::of($accountTransactionCollection)->make(true);

    }
}
