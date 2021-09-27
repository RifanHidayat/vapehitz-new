<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\StudioSale;
use App\Models\StudioSaleTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudioSaleTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('studio-sale-transaction.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sales = StudioSale::with(['studioSaleTransactions'])
            // ->where('customer_id', $customer->id)
            ->orderBy('date', 'DESC')
            // ->where('paid', 0)
            ->get()
            ->each(function ($sale) {
                $sale['total_payment'] = collect($sale->studioSaleTransactions)
                    ->map(function ($transaction) {
                        return $transaction->pivot->amount;
                    })->sum();
            })
            ->filter(function ($sale) {
                return $sale->total_payment < $sale->net_total;
            })
            // ->map(function ($sale) {
            //     return $sale->total_payment;
            // })
            ->values()->all();

        // $sale = CentralSale::with(['customer', 'products'])->findOrFail($id);
        $accounts = Account::all();

        // return $purchase;

        // foreach($purchase->products)
        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['quantity'] = $product->pivot->quantity;
        //     $product['purchase_price'] = $product->pivot->price;
        // });

        // return $selectedProducts;
        // $transactions = collect($sale->centralSaleTransactions)->sortBy('date')->values()->all();
        // $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');

        $sidebarClass = 'compact';

        return view('studio-sale-transaction.create', [
            // 'sale' => $sale,
            'accounts' => $accounts,
            'sales' => $sales,
            // 'total_paid' => $totalPaid,
            // 'transactions' => $transactions,
            'sidebar_class' => $sidebarClass,
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
        //
    }

    public function bulkStore(Request $request)
    {
        $date = $request->date;
        $transactionsByCurrentDateCount = StudioSaleTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'SST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        $amount = $this->clearThousandFormat($request->amount);

        $transaction = new StudioSaleTransaction;
        $transaction->code = $transactionNumber;
        $transaction->date = $request->date;
        $transaction->account_id = $request->account_id;
        // $transaction->customer_id = $request->customer_id;
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

        $customerInvoices = StudioSale::with(['studioSaleTransactions'])
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
                $invoice['total_payment'] = collect($invoice->studioSaleTransactions)
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
                    'studio_sale_id' => $invoice->id,
                    'studio_sale_transaction_id' => $transaction->id,
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
                $item['studio_sale_id'] => [
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
            $transaction->studioSales()->attach($keyedPayments);

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

    public function datatableStudioSaleTransactions()
    {
        $studioSaleTransactions = StudioSaleTransaction::with(['account'])->orderBy('date', 'desc')->select('studio_sale_transactions.*');
        return DataTables::of($studioSaleTransactions)
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
                        <a href="/studio-sale/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
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
