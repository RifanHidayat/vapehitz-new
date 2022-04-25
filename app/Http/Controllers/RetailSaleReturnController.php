<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Product;
use App\Models\RetailSale;
use App\Models\RetailSaleReturn;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RetailSaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('retail-sale-return.index');
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
            $returnsByCurrentDateCount = RetailSaleReturn::query()->where('date', $date)->get()->count();
            $returnNumber = 'RSR/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnsByCurrentDateCount + 1);

            $saleId = $request->sale_id;
            $amount = $this->clearThousandFormat($request->amount);

            $return = new RetailSaleReturn;
            $return->code = $returnNumber;
            $return->date = $request->date;
            $return->retail_sale_id = $request->sale_id;
            $return->account_id = $request->account_id;
            // $return->customer_id = $request->customer_id;
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

            $return->products()->attach($keyedProducts);

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                // $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                if ($product['cause'] == 'defective') {
                    $productRow->bad_stock += $product['return_quantity'];
                } else if ($product['cause'] == 'wrong') {
                    $productRow->retail_stock += $product['return_quantity'];
                }
                $productRow->save();
            }

            // Account Transaction
            // $saleReturn = RetailSaleReturn::find($saleId);
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->note = "Retur penjualan retail No. " . $return->code;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'retail_sale_returns';
            $accountTransaction->table_id = $return->id;
            $accountTransaction->save();

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

        // $saleId = $request->sale_id;

        // $sale = RetailSaleReturn::find($saleId);
        // $transactionAmount = 0;
        // if ($sale !== null) {
        //     $saleTotalPaid = collect($sale->centralSaleTransactions)->sum('amount');
        //     $saleNetTotal = $sale->net_total;
        //     $saleUnpaid = $saleNetTotal - $saleTotalPaid;
        //     if ($amount > $saleUnpaid) {
        //         $transactionAmount = $saleUnpaid;
        //     } else {
        //         $transactionAmount = $amount;
        //     }
        // }

        // Central Sale Return Transaction

        // $returnTransactionsByCurrentDateCount = CentralSaleReturnTransaction::query()->where('date', $date)->get()->count();
        // $returnTransactionNumber = 'SRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnTransactionsByCurrentDateCount + 1);

        // $returnId = $return->id;

        // $returnTransaction = new CentralSaleReturnTransaction;
        // $returnTransaction->code = $returnTransactionNumber;
        // $returnTransaction->date = $request->date;
        // $returnTransaction->account_id = $request->account_id;
        // $returnTransaction->customer_id = $request->customer_id;
        // $returnTransaction->amount = $request->payment_method == 'hutang' ? $transactionAmount : $amount;
        // $returnTransaction->payment_method = $request->payment_method;
        // // $returnTransaction->note = $request->note;

        // try {
        //     $returnTransaction->save();
        //     // return response()->json([
        //     //     'message' => 'Data has been saved',
        //     //     'code' => 200,
        //     //     'error' => false,
        //     //     'data' => $returnTransaction,
        //     // ]);
        // } catch (Exception $e) {
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }

        // try {
        //     $returnTransaction->centralSaleReturns()->attach([
        //         $returnId => [
        //             'amount' => $request->payment_method == 'hutang' ? $transactionAmount : $amount,
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ]);
        // } catch (Exception $e) {
        //     $returnTransaction->delete();
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }

        // Central Sale Transaction

        // if ($request->payment_method == 'hutang') {
        //     $date = $request->date;
        //     $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
        //     $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        //     $transaction = new CentralSaleTransaction;
        //     $transaction->code = $transactionNumber;
        //     $transaction->date = $request->date;
        //     $transaction->account_id = $request->account_id;
        //     $transaction->customer_id = $request->customer_id;
        //     $transaction->amount = $transactionAmount;
        //     $transaction->payment_method = 'hutang';
        //     // $transaction->note = $request->note;

        //     try {
        //         $transaction->save();
        //         // return response()->json([
        //         //     'message' => 'Data has been saved',
        //         //     'code' => 200,
        //         //     'error' => false,
        //         //     'data' => $transaction,
        //         // ]);
        //     } catch (Exception $e) {
        //         return response()->json([
        //             'message' => 'Internal error',
        //             'code' => 500,
        //             'error' => true,
        //             'errors' => $e,
        //         ], 500);
        //     }

        //     try {
        //         $transaction->centralSales()->attach([
        //             $saleId => [
        //                 'amount' => $transactionAmount,
        //                 'created_at' => Carbon::now()->toDateTimeString(),
        //                 'updated_at' => Carbon::now()->toDateTimeString(),
        //             ]
        //         ]);
        //     } catch (Exception $e) {
        //         $transaction->delete();
        //         return response()->json([
        //             'message' => 'Internal error',
        //             'code' => 500,
        //             'error' => true,
        //             'errors' => $e,
        //         ], 500);
        //     }
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $return = RetailSaleReturn::with(['products', 'account'])->findOrFail($id);
        return view('retail-sale-return.show', [
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
        $return = RetailSaleReturn::with(['products'])->findOrFail($id);
        $sale = RetailSale::with(['products'])->findOrFail($return->retail_sale_id);
        $accounts = Account::where('type', '!=', 'none')->get();

        $saleReturnProducts = RetailSaleReturn::with(['products'])
            ->where('retail_sale_id', $sale->id)
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

        return view('retail-sale-return.edit', [
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
            $amount = $this->clearThousandFormat($request->amount);

            $return = RetailSaleReturn::findOrFail($id);
            // $return->code = $returnNumber;
            $return->date = $request->date;
            $return->retail_sale_id = $request->sale_id;
            $return->account_id = $request->account_id;
            // $return->customer_id = $request->customer_id;
            $return->payment_method = $request->payment_method;
            $return->quantity = $request->quantity;
            $return->amount = $amount;
            $return->note = $request->note;

            $products = $request->selected_products;

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

            $return->products()->detach();
            $return->products()->attach($keyedProducts);

            $return->save();

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                // $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                if ($product['old_cause'] == 'defective') {
                    $productRow->bad_stock -= $product['old_return_quantity'];
                } else if ($product['old_cause'] == 'wrong') {
                    $productRow->retail_stock -= $product['old_return_quantity'];
                }

                if ($product['cause'] == 'defective') {
                    $productRow->bad_stock += $product['return_quantity'];
                } else if ($product['cause'] == 'wrong') {
                    $productRow->retail_stock += $product['return_quantity'];
                }
                $productRow->save();
            }

            $accountTransaction = AccountTransaction::where('table_name', 'retail_sale_returns')->where('table_id', $return->id)->first();
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "in";
            $accountTransaction->note = "Retur penjualan retail No. " . $return->code;
            $accountTransaction->date = $request->date;
            $accountTransaction->table_name = 'retail_sale_returns';
            $accountTransaction->table_id = $return->id;

            $accountTransaction->save();

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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        $return = RetailSaleReturn::findOrFail($id);

        $products = $return->products;

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product->pivot->product_id);
                if ($productRow == null) {
                    continue;
                }

                if ($product->pivot->cause == 'defective') {
                    $productRow->bad_stock -= $product->pivot->quantity;
                    // $productRow->retail_stock += $product->pivot->quantity;
                } else if ($product->pivot->cause == 'wrong') {
                    $productRow->retail_stock -= $product->pivot->quantity;
                }
                // $productRow->retail_stock = 100;

                $productRow->save();
            }

            $return->delete();

            $return->products()->detach();

            AccountTransaction::where('table_name', 'retail_sale_returns')->where('table_id', $return->id)->delete();

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

    public function datatableRetailSaleReturns()
    {
        $retailSaleReturns = RetailSaleReturn::with(['products', 'retailSale'])->orderBy('date', 'desc')->select('retail_sale_returns.*');
        return DataTables::of($retailSaleReturns)
            ->addIndexColumn()
            // ->addColumn('shipment_name', function ($row) {
            //     return ($row->shipment ? $row->shipment->name : "");
            // })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/retail-sale-return/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/retail-sale-return/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';

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
