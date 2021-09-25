<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $account = Account::all();
      
        $accountTransactions = AccountTransaction::with(['account'])->where('number','!=',null)->get()->groupBy("number");
      
      //return $accountTransactions['IO/VH/160921/30'];

        $maxid = DB::table('account_transactions')->max('id');
        $number = "IO/VH/" . date('dmy') . "/" . sprintf($maxid + 1);

        return view('account-transaction.index', [
            'account' => $account,
            'accountTransaction' => $accountTransactions,
            'number' => $number,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $accountTransaction = AccountTransaction::all()->groupBy('number');
        return $accountTransaction;

        return view('account-transaction.create', [
            'accountTransaction' => $accountTransaction,
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

        $accountTransaction = new AccountTransaction();
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;
        $accountTransaction->account_type = "out";
       // return $request->account_in;
        try {
            $accountTransaction->save();  
        } catch (Exception $e) {
        
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        
        }
        $accountTransaction = new AccountTransaction();
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_in;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;
        $accountTransaction->account_type = "in";
        try {
            $accountTransaction->save();   
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
        //
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
        //account in
        $accountTransaction = AccountTransaction::find($id);
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_in;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;
        $accountTransaction->account_type = "in";
        try {
            $accountTransaction->save();
           
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }


        $accountTransaction = AccountTransaction::find($id);
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;
        $accountTransaction->account_type = "out";
        try {
            $accountTransaction->save();
           
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
    public function destroy($in_id,$out_id)
    {
        $inTransaction = AccountTransaction::findOrFail($in_id);  
        $outTransaction = AccountTransaction::findOrFail($out_id);        
        try {
            $inTransaction->delete();
            $outTransaction->delete();
            
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        
    }
}
