<?php

namespace App\Http\Controllers;

use App\Models\CentralSale;
use App\Models\CentralSaleTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

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

        try {
            $transaction->save();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $transaction,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // $keyedQuotations = collect($quotations)->mapWithKeys(function ($item) {
        //     return [
        //         $item['id'] => [
        //             'estimation_id' => $item['selected_estimation'],
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ];
        // })->all();

        try {
            $transaction->centralSales()->attach([
                $saleId => [
                    'amount' => $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $transaction,
            ]);
        } catch (Exception $e) {
            $transaction->delete();
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

        try {
            $transaction->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

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
                //  0 + 131_800 = 131_800

                // $invoice['x_remaining_invoice_total'] = $remainingInvoiceTotal;
                // $invoice['x_remaining_payment_amount'] = $remainingPaymentAmount;
                // $invoice['x_amount'] = $amount;
                // $invoice['x_total_invoices'] = $totalInvoices;

                // 391_000 - 131_800 = 
                if (($paymentAmount - $totalInvoices) <= 0) {
                    return false;
                }
            });

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $payments,
        // ]);

        $keyedPayments = collect($payments)->mapWithKeys(function ($item) {
            return [
                $item['central_sale_id'] => [
                    'amount' => $item['amount'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        });

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $payments,
        // ]);

        // $salesIds = collect($payments)->map(function ($item) {
        //     return $item['invoice_id'];
        // })->all();

        try {
            $transaction->centralSales()->attach($keyedPayments);

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $transaction,
            ]);
        } catch (Exception $e) {
            $transaction->delete();
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
        //
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
