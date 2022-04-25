<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralSale;
use App\Models\CentralSaleReturn;
use App\Models\CentralSaleReturnTransaction;
use App\Models\CentralSaleTransaction;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CentralSaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('central-sale-return.index');
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
            $returnsByCurrentDateCount = CentralSaleReturn::query()->where('date', $date)->get()->count();
            $returnNumber = 'CSR/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnsByCurrentDateCount + 1);

            $saleId = $request->sale_id;
            $amount = $this->clearThousandFormat($request->amount);

            $return = new CentralSaleReturn;
            $return->code = $returnNumber;
            $return->date = $request->date;
            $return->central_sale_id = $request->central_sale_id;
            $return->account_id = $request->account_id;
            $return->customer_id = $request->customer_id;
            $return->payment_method = $request->payment_method;
            $return->quantity = $request->quantity;
            $return->amount = $amount;
            $return->note = $request->note;

            $products = $request->selected_products;
            $return->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'quantity' => $item['return_quantity'],
                        'cause' => $item['cause'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            // Attach Return Products
            $return->products()->attach($keyedProducts);

            // Update product stocks
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                // $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                if ($product['cause'] == 'defective') {
                    $productRow->bad_stock += $product['return_quantity'];
                } else if ($product['cause'] == 'wrong') {
                    $productRow->central_stock += $product['return_quantity'];
                }
                $productRow->save();
            }

            $saleId = $request->central_sale_id;

            $sale = CentralSale::find($saleId);
            $transactionAmount = 0;
            if ($sale !== null) {
                $saleTotalPaid = collect($sale->centralSaleTransactions)->sum('amount');
                $saleNetTotal = $sale->net_total;
                $saleUnpaid = $saleNetTotal - $saleTotalPaid;
                if ($amount > $saleUnpaid) {
                    $transactionAmount = $saleUnpaid;
                } else {
                    $transactionAmount = $amount;
                }
            }

            // Central Sale Return Transaction
            $returnTransactionsByCurrentDateCount = CentralSaleReturnTransaction::query()->where('date', $date)->get()->count();
            $returnTransactionNumber = 'SRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnTransactionsByCurrentDateCount + 1);
            $returnId = $return->id;
            $returnTransaction = new CentralSaleReturnTransaction;
            $returnTransaction->code = $returnTransactionNumber;
            $returnTransaction->date = $request->date;
            $returnTransaction->account_id = $request->account_id;
            $returnTransaction->account_type = 'in';
            $returnTransaction->customer_id = $request->customer_id;
            $returnTransaction->amount = $request->payment_method == 'hutang' ? $transactionAmount : $amount;
            $returnTransaction->payment_method = $request->payment_method;
            $returnTransaction->is_init = 1;
            $returnTransaction->note = $request->note;
            $returnTransaction->save();

            // Attach return transaction
            $returnTransaction->centralSaleReturns()->attach([
                $returnId => [
                    'amount' => $request->payment_method == 'hutang' ? $transactionAmount : $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);

            // Insert to account transaction
            if ($request->payment_method == 'transfer' || $request->payment_method == 'cash') {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = $request->account_id;
                $accountTransaction->amount = $amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sale_returns';
                $accountTransaction->table_id = $return->id;

                $accountTransaction->save();
            } else if ($request->payment_method == 'hutang') {
                // if payment method = hutang
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $request->date;
                $transaction->account_id = $request->account_id;
                $transaction->account_type = 'in';
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = 'hutang';
                $transaction->central_sale_return_id = $returnId;
                $transaction->save();

                // attach to central sale transaction
                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $transactionAmount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.piutang', 0);
                $accountTransaction->amount = $transactionAmount;
                $accountTransaction->type = "out";
                $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sale_returns';
                $accountTransaction->table_id = $return->id;
                $accountTransaction->save();

                // Jika jumlah pembayaran melebihi yang telah dibayarkan maka in di hutang,
                if ($amount > $transactionAmount) {
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.hutang', 0);
                    $accountTransaction->amount = $amount - $transactionAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sale_returns';
                    $accountTransaction->table_id = $return->id;
                    $accountTransaction->save();
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $return,
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
        $return = CentralSaleReturn::with(['products', 'account', 'centralSaleReturnTransactions', 'centralSale'])->findOrFail($id);
        return view('central-sale-return.show', [
            'return' => $return,
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
        $return = CentralSaleReturn::with(['products'])->findOrFail($id);
        $sale = CentralSale::with(['products'])->findOrFail($return->central_sale_id);
        $accounts = Account::where('type', '!=', 'none')->get();

        $saleReturnProducts = CentralSaleReturn::with(['products'])
            ->where('central_sale_id', $sale->id)
            ->get()
            ->flatMap(function ($saleReturn) {
                return $saleReturn->products;
            })->groupBy('id')
            ->map(function ($group, $id) {
                $returnedQuantity = collect($group)->map(function ($product) {
                    return $product->pivot->quantity;
                })->sum();
                return [
                    'id' => $id,
                    'returned_quantity' => $returnedQuantity,
                ];
            })
            ->all();

        // return $saleReturnProducts;

        $selectedProducts = collect($sale->products)->each(function ($product) use ($saleReturnProducts, $return) {
            $saleReturn = collect($saleReturnProducts)->where('id', $product->id)->first();
            $returnProduct = collect($return->products)->where('id', $product->id)->first();
            $product['returned_quantity'] = 0;
            $returnProductQuantity = 0;
            $oldCause = '';
            if ($saleReturn !== null && $returnProduct !== null) {
                $returnProductQuantity = $returnProduct->pivot->quantity;
                $oldCause = $returnProduct->pivot->cause;
                $product['returned_quantity'] = $saleReturn['returned_quantity'] - $returnProductQuantity;
            }
            $availableQuantity = $product->pivot->quantity - $product['returned_quantity'];

            $product['return_quantity'] = $returnProductQuantity;
            $product['old_return_quantity'] = $returnProductQuantity;
            $product['cause'] = $oldCause !== '' ? $oldCause : 'defective';
            $product['old_cause'] = $oldCause;
            $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
        })->sortBy('finish')->values()->all();

        // $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');
        $totalPaid = $sale->payment_amount <= 0 ? 0 : $sale->net_total;

        $checkedProducts = collect($return->products)->pluck('id')->all();

        // return $selectedProducts;

        $sidebarClass = 'compact';

        return view('central-sale-return.edit', [
            'return' => $return,
            'sale' => $sale,
            'accounts' => $accounts,
            'total_paid' => $totalPaid,
            'selected_products' => $selectedProducts,
            'sidebar_class' => $sidebarClass,
            'checked_products' => $checkedProducts,
        ]);
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
        DB::beginTransaction();

        try {
            // Save main data
            $amount = $this->clearThousandFormat($request->amount);
            $date = $request->date;

            $return = CentralSaleReturn::findOrFail($id);

            $oldPaymentMethod = $return->payment_method;
            // $return->code = $returnNumber;
            $return->date = $request->date;
            $return->central_sale_id = $request->central_sale_id;
            $return->account_id = $request->account_id;
            $return->customer_id = $request->customer_id;
            $return->payment_method = $request->payment_method;
            $return->quantity = $request->quantity;
            $return->amount = $amount;
            $return->note = $request->note;

            $products = $request->selected_products;
            $return->save();

            // Delete central sale transaction
            if ($oldPaymentMethod == 'hutang') {
                CentralSaleTransaction::query()->where('central_sale_return_id', $return->id)->delete();
            }

            // Delete/Detach init transaction
            $initTransactions = collect($return->centralSaleReturnTransactions)->where('is_init', 1)->pluck('id');
            if (count($initTransactions) > 0) {
                CentralSaleReturnTransaction::query()->whereIn('id', $initTransactions)->delete();
                $return->centralSaleReturnTransactions()->wherePivotIn('central_sale_return_transaction_id', $initTransactions)->detach();
            }

            // Detach product
            $return->products()->detach();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'quantity' => $item['return_quantity'],
                        'cause' => $item['cause'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            // Attach Product
            $return->products()->attach($keyedProducts);

            // Update Stock
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                // $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                if ($product['old_cause'] == 'defective') {
                    $productRow->bad_stock -= $product['old_return_quantity'];
                } else if ($product['old_cause'] == 'wrong') {
                    $productRow->central_stock -= $product['old_return_quantity'];
                }

                if ($product['cause'] == 'defective') {
                    $productRow->bad_stock += $product['return_quantity'];
                } else if ($product['cause'] == 'wrong') {
                    $productRow->central_stock += $product['return_quantity'];
                }
                $productRow->save();
            }

            // Save init Central Sale Return Transaction
            $saleId = $request->central_sale_id;

            $sale = CentralSale::find($saleId);
            $transactionAmount = 0;
            if ($sale !== null) {
                $saleTotalPaid = collect($sale->centralSaleTransactions)->sum('amount');
                $saleNetTotal = $sale->net_total;
                $saleUnpaid = $saleNetTotal - $saleTotalPaid;
                if ($amount > $saleUnpaid) {
                    $transactionAmount = $saleUnpaid;
                } else {
                    $transactionAmount = $amount;
                }
            }

            $returnTransactionsByCurrentDateCount = CentralSaleReturnTransaction::query()->where('date', $date)->get()->count();
            $returnTransactionNumber = 'SRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnTransactionsByCurrentDateCount + 1);

            $returnId = $return->id;

            $returnTransaction = new CentralSaleReturnTransaction;
            $returnTransaction->code = $returnTransactionNumber;
            $returnTransaction->date = $request->date;
            $returnTransaction->account_id = $request->account_id;
            $returnTransaction->customer_id = $request->customer_id;
            $returnTransaction->amount = $request->payment_method == 'hutang' ? $transactionAmount : $amount;
            $returnTransaction->payment_method = $request->payment_method;
            $returnTransaction->is_init = 1;
            // $returnTransaction->note = $request->note;
            $returnTransaction->save();

            $returnTransaction->centralSaleReturns()->attach([
                $returnId => [
                    'amount' => $request->payment_method == 'hutang' ? $transactionAmount : $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
            // End: Save init Central Sale Return Transaction

            // Edit/update Account transaction
            $accountTransaction = AccountTransaction::where('table_name', 'central_sale_returns')->where('table_id', $return->id)->first();
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'central_sale_returns';
            $accountTransaction->table_id = $return->id;
            $accountTransaction->save();

            // Insert central sale transaction
            if ($request->payment_method == 'hutang') {
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $request->date;
                $transaction->account_id = $request->account_id;
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = 'hutang';
                $transaction->central_sale_return_id = $returnId;
                // $transaction->note = $request->note;
                $transaction->save();

                // Attach central sale & central sale transaction
                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $transactionAmount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                // Delete previous account transaction
                AccountTransaction::where('table_name', 'central_sale_returns')->where('table_id', $return->id)->delete();

                // Insert piutang out to account transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.piutang', 0);
                $accountTransaction->amount = $transactionAmount;
                $accountTransaction->type = "out";
                $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sale_returns';
                $accountTransaction->table_id = $return->id;
                $accountTransaction->save();

                if ($amount > $transactionAmount) {
                    $accountTransaction = AccountTransaction::where('table_name', 'central_sale_returns')->where('table_id', $return->id)->first();
                    $accountTransaction->account_id = config('accounts.hutang', 0);
                    $accountTransaction->amount = $amount - $transactionAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Retur penjualan pusat No. " . $return->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sale_returns';
                    $accountTransaction->table_id = $return->id;
                    $accountTransaction->save();
                }
            }
            // End: insert central sale transaction

            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $return,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '[Internal error] error while saving main data',
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

        DB::beginTransaction();

        try {
            $return = CentralSaleReturn::findOrFail($id);
            $products = $return->products;

            // Delete and detach init transaction
            $initTransactions = collect($return->centralSaleReturnTransactions)->where('is_init', 1)->pluck('id');
            if (count($initTransactions) > 0) {
                CentralSaleReturnTransaction::query()->whereIn('id', $initTransactions)->delete();
                $return->centralSaleReturnTransactions()->wherePivotIn('central_sale_return_transaction_id', $initTransactions)->detach();
            }

            // Delete central sale transaction
            if ($return->payment_method == 'hutang') {
                CentralSaleTransaction::query()->where('central_sale_return_id', $return->id)->delete();
            }
            // End: delete central sale transaction

            foreach ($products as $product) {
                $productRow = Product::find($product->pivot->product_id);
                if ($productRow == null) {
                    continue;
                }

                if ($product->pivot->cause == 'defective') {
                    $productRow->bad_stock -= $product->pivot->quantity;
                    // $productRow->central_stock += $product->pivot->quantity;
                } else if ($product->pivot->cause == 'wrong') {
                    $productRow->central_stock -= $product->pivot->quantity;
                }

                $productRow->save();
            }

            // Detach product
            $return->products()->detach();

            // Delete related account transaction
            AccountTransaction::where('table_name', 'central_sale_returns')->where('table_id', $return->id)->delete();

            // Delete main data
            $return->delete();

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

    public function pay($id)
    {
        $saleReturn = CentralSaleReturn::with(['customer', 'products', 'centralSale'])->findOrFail($id);
        $accounts = Account::where('type', '!=', 'none')->get();

        // return $purchase;

        // foreach($purchase->products)
        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['quantity'] = $product->pivot->quantity;
        //     $product['purchase_price'] = $product->pivot->price;
        // });

        // return $selectedProducts;
        $transactions = collect($saleReturn->centralSaleReturnTransactions)->sortBy('date')->values()->all();
        $totalPaid = collect($saleReturn->centralSaleReturnTransactions)->sum('amount');

        $sidebarClass = 'compact';

        // return $transactions;

        return view('central-sale-return.pay', [
            'sale_return' => $saleReturn,
            'accounts' => $accounts,
            'total_paid' => $totalPaid,
            'transactions' => $transactions,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    public function datatableCentralSaleReturns()
    {
        $centralSaleReturns = CentralSaleReturn::with(['centralSale', 'centralSaleReturnTransactions'])->orderBy('date', 'desc')->select('central_sale_returns.*');
        return DataTables::of($centralSaleReturns)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/central-sale-return/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/central-sale-return/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>
                        <a href="/central-sale-return/pay/' . $row->id . '"><em class="icon fas fa-credit-card"></em>
                            <span>Bayar</span>
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
