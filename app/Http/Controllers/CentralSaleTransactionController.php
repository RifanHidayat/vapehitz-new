<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralSale;
use App\Models\CentralSaleTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralSaleTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('central-sale-transaction.index');
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
            $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            $saleId = $request->sale_id;
            $amount = $this->clearThousandFormat($request->amount);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;

            $transaction->save();

            $transaction->centralSales()->attach([
                $saleId => [
                    'amount' => $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);

            // Account Transaction
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->note = "Transaksi pembayaran penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_transactions';
            $accountTransaction->table_id = $transaction->id;
            $accountTransaction->save();


            // Piutang
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = config('accounts.piutang', 0);
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->note = "Transaksi pembayaran penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_transactions';
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

    public function bulkStore(Request $request)
    {
        DB::beginTransaction();
        try {
            $date = $request->date;
            $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            $amount = $this->clearThousandFormat($request->amount);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $request->date;
            $transaction->account_id = $request->account_id;
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            $transaction->payment_method = $request->payment_method;
            $transaction->note = $request->note;
            $sales = $request->selected_sales;

            $transaction->save();

            $payments = [];
            $totalInvoices = 0;
            $paymentAmount = $amount;

            // $customerInvoices = collect($sales)
            $selectedInvoicesIds = collect($sales)->pluck('id')->all();

            $customerInvoices = CentralSale::with(['centralSaleTransactions'])
                ->whereIn('id', $selectedInvoicesIds)
                ->orderBy('date', 'ASC')
                // ->where('paid', 0)
                ->get()
                // ->each(function ($invoice) {
                //     $invoice['total_payment'] = collect($invoice->payments)->sum('amount');
                // })
                // ->filter(function ($invoice) {
                //     return $invoice->total_payment < $invoice->total;
                // })
                ->each(function ($invoice) {
                    $invoice['total_payment'] = collect($invoice->centralSaleTransactions)
                        ->map(function ($transaction) {
                            return $transaction->pivot->amount;
                        })->sum();
                })
                ->filter(function ($invoice) {
                    return $invoice->total_payment < $invoice->net_total;
                })
                ->each(function ($invoice) use ($paymentAmount, &$totalInvoices, &$payments, $transaction) {
                    // Remaining Debt Has To Pay Per Invoice
                    $remainingInvoiceTotal = $invoice->net_total - $invoice->total_payment;
                    // 131_800 - 0 = 131_800;

                    $amount = $remainingInvoiceTotal;

                    $remainingPaymentAmount = $paymentAmount - $totalInvoices;
                    // 391_000 - 0 = 391_900;

                    // 131_800 > 391_000 = FALSE
                    if ($remainingInvoiceTotal > $remainingPaymentAmount) {
                        $amount = $remainingPaymentAmount;
                    }

                    $payment = [
                        'central_sale_id' => $invoice->id,
                        'central_sale_transaction_id' => $transaction->id,
                        'amount' => $amount,
                    ];

                    array_push($payments, $payment);

                    $totalInvoices = $totalInvoices + $remainingInvoiceTotal;

                    if (($paymentAmount - $totalInvoices) <= 0) {
                        return false;
                    }
                });

            $keyedPayments = collect($payments)->mapWithKeys(function ($item) {
                return [
                    $item['central_sale_id'] => [
                        'amount' => $item['amount'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            });

            $transaction->centralSales()->attach($keyedPayments);

            // Account Transaction
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->note = "Transaksi pembayaran penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_transactions';
            $accountTransaction->table_id = $transaction->id;
            $accountTransaction->save();

            // Out Piutang
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = config('accounts.piutang', 0);
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->note = "Transaksi pembayaran penjualan pusat No. " . $transactionNumber;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_transactions';
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
        $transaction = CentralSaleTransaction::with(['centralSales', 'customer', 'centralSaleReturn'])->findOrFail($id);

        return view('central-sale-transaction.show', [
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
            $transaction = CentralSaleTransaction::findOrFail($id);
            $transaction->centralSales()->detach();

            // Delete Account Transaction
            AccountTransaction::where('table_name', 'central_sale_transactions')->where('table_id', $transaction->id)->delete();

            $transaction->delete();

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
                'message' => 'Internal error detaching',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    public function datatableCentralSaleTransactions()
    {
        // $centralSaleTransactions = CentralSaleTransaction::with(['account'])->orderBy('date', 'desc')->select('central_sale_transactions.*');
        // return DataTables::of($centralSaleTransactions)
        //     ->addIndexColumn()
        //     ->addColumn('action', function ($row) {
        //         $deleteButton = '';

        //         if ($row->is_init !== 1) {
        //             $deleteButton = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
        //                 <span>Delete</span>
        //                 </a>';
        //         }

        //         $button = '
        //     <div class="dropright">
        //         <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
        //         <div class="dropdown-menu dropdown-menu-right">
        //             <ul class="link-list-opt no-bdr">
                        
        //                 ' . $deleteButton . '
        //                 <a href="/central-sale-transaction/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
        //                     <span>Detail</span>
        //                 </a>';


        //         //     if ($row->status == 'pending') {
        //         //         $button .= '<a href="/central-sale/approval/' . $row->id . '"><em class="icon fas fa-check"></em>
        //         //     <span>Approval</span>
        //         // </a>';
        //         //     }

        //         $button .= '           
        //             </ul>
        //         </div>
        //     </div>';

        //         return $button;
        //     })
        //     ->rawColumns(['action'])
        //     ->make(true);
        $centralSaleTransactions = CentralSaleTransaction::with(['account', 'centralSales'])->select('central_sale_transactions.*');
        return DataTables::of($centralSaleTransactions)
            ->addIndexColumn()
            ->addColumn('invoice_number', function (CentralSaleTransaction $transaction) {
                return $transaction->centralSales->map(function ($invoice) {
                    return '<a href="/central-sale/show/' . $invoice->id . '" target="_blank">' . $invoice->code . '</a';
                })->implode(", ");
            })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/central-sale-transaction/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';


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
            ->rawColumns(['action', 'invoice_number'])
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
