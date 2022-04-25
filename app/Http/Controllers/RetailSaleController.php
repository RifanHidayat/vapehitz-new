<?php

namespace App\Http\Controllers;

use App\Exports\RetailSaleDetailExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Product;
use App\Models\RetailSale;
use App\Models\RetailSaleReturn;
use App\Models\RetailSaleTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class RetailSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('retail-sale.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $accounts = Account::where('type', '!=', 'none')->get();
        $maxid = DB::table('central_purchases')->max('id');
        $code = "PO/VH/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);

        $sidebarClass = 'compact';

        return view('retail-sale.create', [
            'code' => $code,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'sidebar_class' => $sidebarClass
        ]);

        // return view('retail-sale.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $request->all(),
        // ]);
        // $retailSale->note = $request->note;
        $products = $request->selected_products;

        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                if ($productRow == null) {
                    continue;
                }

                $taken = $product['quantity'] + $product['free'];
                if ($taken > $productRow->retail_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['retail_stock'] = $productRow->retail_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    // $product['editable'] = 0;
                    $product['subTotal'] = $product['retail_price'];
                    $product['backgroundColor'] = 'bg-pink-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
            }

            if ($unavailableStockProductCount > 0) {
                return response()->json([
                    'message' => 'Insufficient stock',
                    'data' => [
                        'selected_products' => $newSelectedProducts,
                    ],
                    'code' => 400,
                    'error' => true,
                    'error_type' => 'unsufficient_stock'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        DB::beginTransaction();

        try {
            $date = $request->date;

            $retailSalesByCurrentDateCount = RetailSale::query()->whereDate('date', $date)->get()->count();
            $retailSaleNumber = 'RS/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $retailSalesByCurrentDateCount + 1);

            $netTotal = $this->clearThousandFormat($request->net_total);
            $paymentAmount = $this->clearThousandFormat($request->pay_amount);

            $retailSale = new RetailSale;
            $retailSale->code = $retailSaleNumber;
            $retailSale->date = $request->date . ' ' . date('H:i:s');
            $retailSale->total_weight = $request->total_weight;
            // $retailSale->total_cost = $request->total_cost;
            $retailSale->discount = $this->clearThousandFormat($request->discount);
            $retailSale->discount_type = $request->discount_type;
            $retailSale->subtotal = $this->clearThousandFormat($request->subtotal);
            $retailSale->shipping_cost = $this->clearThousandFormat($request->shipping_cost);
            $retailSale->other_cost = $this->clearThousandFormat($request->other_cost);
            $retailSale->detail_other_cost = $request->detail_other_cost;
            // $retailSale->deposit_customer = $request->deposit_customer;
            $retailSale->net_total = $this->clearThousandFormat($request->net_total);
            $retailSale->payment_amount = $this->clearThousandFormat($request->pay_amount);
            $retailSale->payment_method = $request->payment_method;
            $retailSale->account_id = $request->account_id;
            $retailSale->created_by = Auth::id();
            $retailSale->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['retail_stock'],
                        // 'booked' => $item['booked'],
                        // 'price' => str_replace(".", "", $item['price']),
                        'price' => $this->clearThousandFormat($item['price']),
                        'quantity' => $item['quantity'],
                        'free' => $item['free'],
                        // 'amount' => $item['subTotal'],
                        // 'editable' => $item['editable'] == true ? 1 : 0,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            $retailSale->products()->attach($keyedProducts);

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->retail_stock -= ($product['quantity'] + $product['free']);
                $productRow->save();
            }

            // Retail Sale Transaction
            if ($request->payment_method !== 'hutang') {
                $date = $request->date;
                $transactionsByCurrentDateCount = RetailSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $retailSale->id;
                $transactionNumber = 'RST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $transactionAmount = $paymentAmount > $netTotal ? $netTotal : $paymentAmount;
                // $amount = $this->clearThousandFormat($transactionAmount);

                $transaction = new RetailSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->account_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = $request->payment_method;
                $transaction->account_type = 'in';
                $transaction->is_init = 1;

                $transaction->save();

                $transaction->retailSales()->attach([
                    $saleId => [
                        'amount' => $transactionAmount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                if ($paymentAmount < $netTotal) {
                    if ($paymentAmount > 0) {
                        // Account Transaction
                        $accountTransaction = new AccountTransaction;
                        $accountTransaction->account_id = $request->account_id;
                        $accountTransaction->amount = $paymentAmount;
                        $accountTransaction->type = "in";
                        $accountTransaction->note = "Penjualan retail No. " . $retailSaleNumber;
                        $accountTransaction->date = $request->date;
                        $accountTransaction->table_name = 'retail_sales';
                        $accountTransaction->table_id = $retailSale->id;
                        $accountTransaction->save();
                    }

                    // Account Transaction: in piutang
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $netTotal - $paymentAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan retail No. " . $retailSaleNumber;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'retail_sales';
                    $accountTransaction->table_id = $retailSale->id;
                    $accountTransaction->save();
                } else {
                    // Account Transaction
                    // Jika jumlah pembayaran melebihi net total
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->account_id;
                    $accountTransaction->amount = $netTotal;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan retail No. " . $retailSaleNumber;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'retail_sales';
                    $accountTransaction->table_id = $retailSale->id;
                    $accountTransaction->save();
                }
            } else {
                // Jika Hutang
                // Account Transaction
                try {
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $netTotal;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan retail No. " . $retailSaleNumber;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'retail_sales';
                    $accountTransaction->table_id = $retailSale->id;
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

            // -------- DISCOUNT
            $discountAmount = $this->clearThousandFormat($request->discount);
            if ($discountAmount > 0) {
                $discountType = $request->discount_type;

                $finalDiscountAmount = $discountAmount;

                if ($discountType == 'percentage') {
                    $finalDiscountAmount = $this->clearThousandFormat($request->subtotal) * ($discountAmount / 100);
                }

                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.sale_discount', 0);
                $accountTransaction->amount = $finalDiscountAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Diskon penjualan retail No. " . $retailSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'retail_sales';
                $accountTransaction->table_id = $retailSale->id;
                $accountTransaction->save();
            }
            // --------

            // -------- SHIPPING COST
            $shippingAmount = $this->clearThousandFormat($request->shipping_cost);
            if ($shippingAmount > 0) {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.sale_shipping_cost', 0);
                $accountTransaction->amount = $shippingAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Biaya kirim penjualan retail No. " . $retailSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'retail_sales';
                $accountTransaction->table_id = $retailSale->id;
                $accountTransaction->save();
            }
            // --------

            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $retailSale,
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
        $sale = RetailSale::with('products.productCategory')->findOrFail($id);
        $transactions = collect($sale->retailSaleTransactions)->sortBy('date')->values()->all();
        $returns = collect($sale->retailSaleReturns)->sortBy('date')->values()->all();
        // $returns = CentralSaleReturn::with(['products'])->where('central_sale_id', $id)->get();
        return view('retail-sale.show', [
            'sale' => $sale,
            'transactions' => $transactions,
            'returns' => $returns,
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
        $sale = RetailSale::with(['products'])->findOrFail($id);

        $accounts = Account::where('type', '!=', 'none')->get();
        $suppliers = Supplier::all();

        $selectedProducts = $sale->products;

        $oldTakenProducts = collect($sale->products)->groupBy('id')->map(function ($group, $productId) {
            return [
                'id' => $productId,
                'taken' => collect($group)->sum(function ($product) {
                    return $product->pivot->quantity + $product->pivot->free;
                }),
            ];
        })->values()->all();

        foreach ($selectedProducts as $product) {
            $productRow = Product::find($product['id']);

            if ($productRow == null) {
                continue;
            }

            $product['retail_stock'] = $productRow->retail_stock + ($product->pivot->quantity + $product->pivot->free);
            $product['quantity'] = $product->pivot->quantity;
            $product['free'] = $product->pivot->free;
            $product['old_taken'] = $product->pivot->quantity + $product->pivot->free;
            $product['price'] = $product->pivot->price;
        }

        $sidebarClass = 'compact';

        return view('retail-sale.edit', [
            'sale' => $sale,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'selected_products' => $selectedProducts,
            'sidebar_class' => $sidebarClass,
            'old_taken_products' => $oldTakenProducts,
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
        $retailSale = RetailSale::findOrFail($id);
        $products = $request->selected_products;
        $oldTakenProducts = $request->old_taken_products;

        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                if ($productRow == null) {
                    continue;
                }

                // $taken = $product['quantity'] + $product['free'];

                // $oldTaken = 0;
                // if (array_key_exists('old_taken', $product)) {
                //     // $taken = ($productRow->quantity - $product['old_quantity']) + $product['quantity'] + $product['free'];
                //     $oldTaken = $product['old_taken'];
                // }
                $oldTakenProduct = collect($oldTakenProducts)->where('id', $product['id'])->first();

                $taken = 0;
                $oldTakenQuantity = 0;

                if ($oldTakenProduct !== null) {
                    $oldTakenQuantity = $oldTakenProduct['taken'];
                }

                // stok 5 ambil 3 = 2
                // edit
                // stok 2 ambil 5
                // 6 - 3 = 3 > 2 = TRUE
                // 5 - 3 = 2 > 2 = FALSE

                // if ($retailSale->status == 'pending') {
                //     $taken = ($productRow->booked - $oldTakenQuantity) + $product['quantity'] + $product['free'];
                // } else if ($retailSale->status == 'approved') {
                // }
                $taken = ($product['quantity'] + $product['free']) - $oldTakenQuantity;

                if ($taken > $productRow->retail_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['retail_stock'] = $productRow->retail_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    // $product['editable'] = 0;
                    $product['subTotal'] = $product['retail_price'];
                    $product['backgroundColor'] = 'bg-pink-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
            }

            if ($unavailableStockProductCount > 0) {
                return response()->json([
                    'message' => 'Insufficient stock',
                    'data' => [
                        'selected_products' => $newSelectedProducts,
                    ],
                    'code' => 400,
                    'error' => true,
                    'error_type' => 'unsufficient_stock'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        DB::beginTransaction();

        try {
            $netTotal = $this->clearThousandFormat($request->net_total);
            $paymentAmount = $this->clearThousandFormat($request->pay_amount);

            $oldAccountId = $retailSale->account_id;
            $oldNetTotal = $retailSale->net_total;
            // $retailSale->code = $retailSaleNumber;
            $retailSale->date = $request->date . ' ' . date('H:i:s');
            $retailSale->total_weight = $request->total_weight;
            $retailSale->discount = $this->clearThousandFormat($request->discount);
            $retailSale->discount_type = $request->discount_type;
            $retailSale->subtotal = $this->clearThousandFormat($request->subtotal);
            $retailSale->shipping_cost = $this->clearThousandFormat($request->shipping_cost);
            $retailSale->other_cost = $this->clearThousandFormat($request->other_cost);
            $retailSale->detail_other_cost = $request->detail_other_cost;
            $retailSale->net_total = $this->clearThousandFormat($request->net_total);
            $retailSale->payment_amount = $this->clearThousandFormat($request->pay_amount);
            $retailSale->payment_method = $request->payment_method;
            $retailSale->account_id = $request->account_id;
            $retailSale->updated_by = Auth::id();
            $retailSale->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['retail_stock'],
                        'price' => $this->clearThousandFormat($item['price']),
                        'quantity' => $item['quantity'],
                        'free' => $item['free'],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            $retailSale->products()->detach();
            $retailSale->products()->attach($keyedProducts);

            foreach ($oldTakenProducts as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->retail_stock = $productRow->retail_stock + $product['taken'];
                $productRow->save();
            }

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                // $oldTaken = 0;
                // if (array_key_exists('old_taken', $product)) {
                //     $oldTaken = $product['old_taken'];
                // }

                // $productRow->retail_stock = ($productRow->retail_stock + $oldTaken) - ($product['quantity'] + $product['free']);
                $productRow->retail_stock = $productRow->retail_stock - ($product['quantity'] + $product['free']);
                $productRow->save();
            }

            AccountTransaction::where('table_name', 'retail_sales')->where('table_id', $retailSale->id)->delete();

            $initTransactions = collect($retailSale->retailSaleTransactions)->where('is_init', 1)->pluck('id');
            if (count($initTransactions) > 0) {
                RetailSaleTransaction::query()->whereIn('id', $initTransactions)->delete();
                $retailSale->retailSaleTransactions()->wherePivotIn('retail_sale_transaction_id', $initTransactions)->detach();
            }

            // Retail Sale Transaction
            if ($request->payment_method !== 'hutang') {
                $date = $request->date;
                $transactionsByCurrentDateCount = RetailSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $retailSale->id;
                $transactionNumber = 'RST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $transactionAmount = $paymentAmount > $netTotal ? $netTotal : $paymentAmount;
                // $amount = $this->clearThousandFormat($transactionAmount);

                $transaction = new RetailSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->account_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = $request->payment_method;
                $transaction->account_type = 'in';
                $transaction->is_init = 1;

                $transaction->save();

                $transaction->retailSales()->attach([
                    $saleId => [
                        'amount' => $transactionAmount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                if ($paymentAmount < $netTotal) {
                    if ($paymentAmount > 0) {
                        // Account Transaction
                        $accountTransaction = new AccountTransaction;
                        $accountTransaction->account_id = $request->account_id;
                        $accountTransaction->amount = $paymentAmount;
                        $accountTransaction->type = "in";
                        $accountTransaction->note = "Penjualan retail No. " . $retailSale->code;
                        $accountTransaction->date = $request->date;
                        $accountTransaction->table_name = 'retail_sales';
                        $accountTransaction->table_id = $retailSale->id;

                        $accountTransaction->save();
                    }

                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $netTotal - $paymentAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan retail No. " . $retailSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'retail_sales';
                    $accountTransaction->table_id = $retailSale->id;

                    $accountTransaction->save();
                } else {
                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->account_id;
                    $accountTransaction->amount = $netTotal;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan retail No. " . $retailSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'retail_sales';
                    $accountTransaction->table_id = $retailSale->id;

                    $accountTransaction->save();
                }
            } else {
                // Jika Hutang
                // Account Transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.piutang', 0);
                $accountTransaction->amount = $netTotal;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Penjualan retail No. " . $retailSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'retail_sales';
                $accountTransaction->table_id = $retailSale->id;

                $accountTransaction->save();
            }

            // -------- DISCOUNT
            $discountAmount = $this->clearThousandFormat($request->discount);
            if ($discountAmount > 0) {
                $discountType = $request->discount_type;

                $finalDiscountAmount = $discountAmount;

                if ($discountType == 'percentage') {
                    $finalDiscountAmount = $this->clearThousandFormat($request->subtotal) * ($discountAmount / 100);
                }

                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.sale_discount', 0);
                $accountTransaction->amount = $finalDiscountAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Diskon penjualan retail No. " . $retailSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'retail_sales';
                $accountTransaction->table_id = $retailSale->id;
                $accountTransaction->save();
            }
            // --------

            // -------- SHIPPING COST
            $shippingAmount = $this->clearThousandFormat($request->shipping_cost);
            if ($shippingAmount > 0) {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.sale_shipping_cost', 0);
                $accountTransaction->amount = $shippingAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Biaya kirim penjualan retail No. " . $retailSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'retail_sales';
                $accountTransaction->table_id = $retailSale->id;
                $accountTransaction->save();
            }
            // --------

            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $retailSale,
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
        $sale = RetailSale::findOrFail($id);
        $products = $sale->products;

        $saleReturns = RetailSaleReturn::with(['products'])
            ->where('retail_sale_id', $id)
            ->get()
            ->flatMap(function ($return) {
                return $return->products;
            })
            ->groupBy('id')
            ->map(function ($products, $productId) {
                // $returnedQuantity = collect($products)->sum(function ($product) {
                //     return $product->pivot->quantity;
                // });

                // return [
                //     'id' => $productId,
                //     'returned_quantity' => $returnedQuantity,
                // ];

                $wrongQuantity = collect($products)->filter(function ($product) {
                    return $product->pivot->cause = 'wrong';
                })->sum(function ($product) {
                    return $product->pivot->quantity;
                });

                $defectiveQuantity = collect($products)->filter(function ($product) {
                    return $product->pivot->cause = 'defective';
                })->sum(function ($product) {
                    return $product->pivot->quantity;
                });

                return [
                    'id' => $productId,
                    'wrong_quantity' => $wrongQuantity,
                    'defective_quantity' => $defectiveQuantity,
                ];
            })->values()->all();

        // return collect($saleReturns)->where('id', 1)->first();

        DB::beginTransaction();

        // Update Stock
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                $returnedQuantity = 0;
                $wrongQuantity = 0;
                $defectiveQuantity = 0;
                $productReturn = collect($saleReturns)->where('id', $product['id'])->first();
                if ($productReturn !== null) {
                    // $returnedQuantity = $productReturn['returned_quantity'];
                    $wrongQuantity = $productReturn['wrong_quantity'];
                    $defectiveQuantity = $productReturn['defective_quantity'];
                }

                $productRow->bad_stock = $productRow->bad_stock - $defectiveQuantity;
                $productRow->retail_stock = $productRow->retail_stock + ($product->pivot->quantity - ($wrongQuantity + $defectiveQuantity)) + $product->pivot->free;
                $productRow->save();
            }

            // Delete Related Return 
            RetailSaleReturn::query()->where('retail_sale_id', $id)->delete();

            // Detach Product From Intermediate Table
            $sale->products()->detach();

            // Delete retail sale
            $retailSaleTransactionIds = collect($sale->retailSaleTransactions)->pluck('id')->all();
            // Delete sale transaction account transaction 
            if (count($retailSaleTransactionIds) > 0) {
                DB::table('retail_sale_transactions')->whereIn('id', $retailSaleTransactionIds)->delete();
                AccountTransaction::where('table_name', 'retail_sale_transactions')->whereIn('table_id', $retailSaleTransactionIds)->delete();
            }

            // Account Transaction
            AccountTransaction::where('table_name', 'retail_sales')->where('table_id', $sale->id)->delete();

            // Delete Main Data
            $sale->delete();

            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $sale,
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

    public function return($id)
    {

        // return 'asdasd';
        $sale = RetailSale::with(['products'])->findOrFail($id);
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

        $selectedProducts = collect($sale->products)->each(function ($product) use ($saleReturnProducts) {
            $saleReturn = collect($saleReturnProducts)->where('id', $product->id)->first();
            $product['returned_quantity'] = 0;
            if ($saleReturn !== null) {
                $product['returned_quantity'] = $saleReturn['returned_quantity'];
            }
            $availableQuantity = $product->pivot->quantity - $product['returned_quantity'];

            $product['return_quantity'] = $availableQuantity;
            $product['cause'] = 'defective';
            $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
        })->sortBy('finish')->values()->all();

        // $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');
        $totalPaid = $sale->payment_amount <= 0 ? 0 : $sale->net_total;

        // return $transactions;
        // return $selectedProducts;

        // return $selectedProducts;
        $sidebarClass = 'compact';

        return view('retail-sale.return', [
            'sale' => $sale,
            'accounts' => $accounts,
            'total_paid' => $totalPaid,
            'selected_products' => $selectedProducts,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    public function print($id)
    {
        // return view('central-sale.print');
        $sale = RetailSale::with(['products', 'createdBy'])->findOrFail($id);

        $data = [
            'sale' => $sale,
        ];

        // $pdf = PDF::loadView('retail-sale.print-80mm', $data, [], [
        //     'format' => [80, 1000],
        //     'margin_top' => 0,
        //     'margin_right' => 0,
        //     'margin_bottom' => 0,
        //     'margin_left' => 0,
        // ]);

        // return $pdf->stream($sale->code . '.pdf');

        return view('retail-sale.print-80mm', $data);
    }

    public function datatableRetailSale()
    {
        $retailSale = RetailSale::with(['products', 'createdBy'])->select('retail_sales.*');
        return DataTables::of($retailSale)
            ->addIndexColumn()
            ->addColumn('sales_name', function ($row) {
                return ($row->createdBy ? $row->createdBy->name : "");
            })
            // ->addColumn('shipment_name', function ($row) {
            //     return ($row->shipment ? $row->shipment->name : "");
            // })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/retail-sale/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/retail-sale/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';

                $button .= '<a href="/retail-sale/return/' . $row->id . '"><em class="icon fas fa-undo"></em><span>Retur</span></a>';
                $button .= '<a href="/retail-sale/print/' . $row->id . '" target="_blank"><em class="icon fas fa-print"></em><span>Cetak</span></a>';



                $button .= '           
                    </ul>
                </div>
            </div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function report(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new RetailSaleDetailExport($request->all()), 'Retail Sales Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
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
