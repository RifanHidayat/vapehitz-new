<?php

namespace App\Http\Controllers;

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
        $accountTransaction = AccountTransaction::all();
        $maxid = DB::table('account_transactions')->max('id');
        $number = "IO/VH/" . date('dmy') . "/" . sprintf($maxid + 1);
        return view('account-transaction.index', [
            'accountTransaction' => $accountTransaction,
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
        $accountTransaction = AccountTransaction::all();
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
        $accountTransaction->account_in = $request->account_in;
        $accountTransaction->account_out = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;

        try {
            $accountTransaction->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $accountTransaction,
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
        $accountTransaction = AccountTransaction::find($id);
        $accountTransaction->number = $request->number;
        $accountTransaction->account_in = $request->account_in;
        $accountTransaction->account_out = $request->account_out;
        $accountTransaction->date = $request->date;
        $accountTransaction->amount = $request->amount;
        $accountTransaction->note = $request->note;
        try {
            $accountTransaction->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $accountTransaction,
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
        $accountTransaction = AccountTransaction::findOrFail($id);
        try {
            $accountTransaction->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $accountTransaction,
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
}
