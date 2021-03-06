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

        //account out
        $accountTransaction = new AccountTransaction();
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = str_replace(".", "", $request->amount);
        $accountTransaction->note = $request->note;
        $accountTransaction->type = "out";
        $accountTransaction->description="Transaksi In Out";

       // return $request->account_in;

         DB::beginTransaction();
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
        //account in
        $accountTransaction = new AccountTransaction();
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_in;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = str_replace(".", "", $request->amount);
        $accountTransaction->note = $request->note;
        $accountTransaction->type = "in";
          $accountTransaction->description="Transaksi In Out";
        try {
            $accountTransaction->save();  
             DB::commit(); 
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
        $accountTransaction = AccountTransaction::find($request->in_id);
        $accountTransaction->number = $request->number;
        $accountTransaction->account_id = $request->account_in;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = str_replace(".", "", $request->amount);
        $accountTransaction->note = $request->note;
        $accountTransaction->type = "in";
        
          DB::beginTransaction();
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

        //acccount out
        $accountTransaction = AccountTransaction::find($request->out_id);
      
        $accountTransaction->account_id = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = str_replace(".", "", $request->amount);
        $accountTransaction->note = $request->note;
        $accountTransaction->type = "out";
        try {
            $accountTransaction->save();
             DB::commit();
            
           
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($in_id,$out_id)
    {
        $inTransaction = AccountTransaction::findOrFail($in_id);  
        $outTransaction = AccountTransaction::findOrFail($out_id);   
        DB::beginTransaction();     
        try {
            $inTransaction->delete();
            $outTransaction->delete();
             DB::commit();
            
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

    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }

    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }
}
