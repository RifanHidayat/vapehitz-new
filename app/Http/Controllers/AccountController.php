<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\PurchaseReturnTransaction;
use App\Models\PurchaseTransaction;
use Exception;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\Facades\DataTables;

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

        // $account = Account::with(['accountTransactions'])->findOrFail($id);
        // return $account['accountTransactions'];
        
        //return $account;
        return view('account.show', [
            "account_id"=>$id
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
        $account = AccountTransaction::with(['account'])->select('account_transactions.*')->where('account_id','=',$id); 

        return DataTables::eloquent($account)
            ->addIndexColumn()

            ->addColumn('Type', function ($row) {
                return ($row->type=="in"?"Deposit":"Expense"); 
            })
            ->addColumn('in', function ($row) {
                return ($row->type=="in"?number_format($row->amount):""); 
            })
            ->addColumn('out', function ($row) {
                return ($row->type=="out"?number_format($row->amount):""); 
            })
            ->addColumn('balance', function ($row) {
                return (number_format($row->amount)); 
            })
            ->addColumn('action', function ($row) {
                $button = '';
                return $button;
            }
            )
            ->make(true);
    }
}
