<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\CentralSaleReturn;
use App\Models\CentralSaleReturnTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralSaleReturnTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('central-sale-return-transaction.index');
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $date = $request->date;
            $transactionsByCurrentDateCount = CentralSaleReturnTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'SRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            $saleReturnId = $request->sale_return_id;
            $amount = $this->clearThousandFormat($request->amount);

            $transaction = new CentralSaleReturnTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;
            $transaction->save();

            $transaction->centralSaleReturns()->attach([
                $saleReturnId => [
                    'amount' => $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);

            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->note = "Penyelesaian retur penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_return_transactions';
            $accountTransaction->table_id = $transaction->id;
            $accountTransaction->save();

            // Account Transaction
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = config('accounts.hutang', 0);
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->note = "Penyelesaian retur penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_return_transactions';
            $accountTransaction->table_id = $transaction->id;
            $accountTransaction->save();

            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $transaction,
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
        $transaction = CentralSaleReturnTransaction::with(['centralSaleReturns'])->findOrFail($id);

        return view('central-sale-return-transaction.show', [
            'transaction' => $transaction,
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
        try {
            $transaction = CentralSaleReturnTransaction::findOrFail($id);
            $transaction->centralSaleReturns()->detach();

            AccountTransaction::where('table_name', 'central_sale_return_transactions')->where('table_id', $transaction->id)->delete();

            $transaction->delete();
            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error detaching',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    public function datatableCentralSaleReturnTransactions()
    {
        $centralSaleReturnTransactions = CentralSaleReturnTransaction::with(['centralSaleReturns'])->orderBy('date', 'desc')->select('central_sale_return_transactions.*');
        return DataTables::of($centralSaleReturnTransactions)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                    
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/central-sale-return-transaction/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>
                        ';


                //     if ($row->status == 'pending') {
                //         $button .= '<a href="/central-sale/approval/' . $row->id . '"><em class="icon fas fa-check"></em>
                //     <span>Approval</span>
                // </a>';
                //     }

                $button .= '           
                    </ul>
                </div>
            </div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
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
