<?php

namespace App\Http\Controllers;

use App\Exports\StudioSaleDetailExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Product;
use App\Models\StudioSale;
use App\Models\StudioSaleReturn;
use App\Models\StudioSaleTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class StudioSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('studio-sale.index');
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

        return view('studio-sale.create', [
            'code' => $code,
            'accounts' => $accounts,
            'suppliers' => $suppliers,
            'sidebar_class' => $sidebarClass
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
        // $sale->note = $request->note;
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
                if ($taken > $productRow->studio_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['studio_stock'] = $productRow->studio_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    // $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
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

            $salesByCurrentDateCount = StudioSale::query()->whereDate('date', $date)->get()->count();
            $saleNumber = 'SS/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $salesByCurrentDateCount + 1);

            $netTotal = $this->clearThousandFormat($request->net_total);
            $paymentAmount = $this->clearThousandFormat($request->pay_amount);

            $sale = new StudioSale;
            $sale->code = $saleNumber;
            $sale->date = $request->date . ' ' . date('H:i:s');
            $sale->total_weight = $request->total_weight;
            // $sale->total_cost = $request->total_cost;
            $sale->discount = $this->clearThousandFormat($request->discount);
            $sale->discount_type = $request->discount_type;
            $sale->subtotal = $this->clearThousandFormat($request->subtotal);
            $sale->shipping_cost = $this->clearThousandFormat($request->shipping_cost);
            $sale->other_cost = $this->clearThousandFormat($request->other_cost);
            $sale->detail_other_cost = $request->detail_other_cost;
            // $sale->deposit_customer = $request->deposit_customer;
            $sale->net_total = $this->clearThousandFormat($request->net_total);
            $sale->payment_amount = $this->clearThousandFormat($request->pay_amount);
            $sale->payment_method = $request->payment_method;
            $sale->account_id = $request->account_id;
            $sale->created_by = Auth::id();

            $sale->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['studio_stock'],
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

            $sale->products()->attach($keyedProducts);

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->studio_stock -= ($product['quantity'] + $product['free']);
                $productRow->save();
            }

            if ($request->payment_method !== 'hutang') {
                $date = $request->date;
                $transactionsByCurrentDateCount = StudioSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $sale->id;
                $transactionNumber = 'SST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $transactionAmount = $paymentAmount > $netTotal ? $netTotal : $paymentAmount;
                // $amount = $this->clearThousandFormat($transactionAmount);

                $transaction = new StudioSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->account_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = $request->payment_method;
                $transaction->account_type = 'in';
                $transaction->is_init = 1;

                $transaction->save();

                $transaction->studioSales()->attach([
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
                        $accountTransaction->note = "Penjualan studio No. " . $saleNumber;
                        $accountTransaction->date = $request->date;
                        $accountTransaction->table_name = 'studio_sales';
                        $accountTransaction->table_id = $sale->id;
                        $accountTransaction->save();
                    }

                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $netTotal - $paymentAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan studio No. " . $saleNumber;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'studio_sales';
                    $accountTransaction->table_id = $sale->id;

                    $accountTransaction->save();
                } else {
                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->account_id;
                    $accountTransaction->amount = $netTotal;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan studio No. " . $saleNumber;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'studio_sales';
                    $accountTransaction->table_id = $sale->id;

                    $accountTransaction->save();
                }
            } else {
                // Jika Hutang
                // Account Transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('piutang', 0);
                $accountTransaction->amount = $netTotal;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Penjualan studio No. " . $saleNumber;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $sale->id;
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
                $accountTransaction->note = "Diskon penjualan studio No. " . $sale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $sale->id;
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
                $accountTransaction->note = "Biaya kirim penjualan studio No. " . $sale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $sale->id;
                $accountTransaction->save();
            }
            // --------

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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sale = StudioSale::with('products.productCategory')->findOrFail($id);
        $transactions = collect($sale->studioSaleTransactions)->sortBy('date')->values()->all();
        $returns = collect($sale->studioSaleReturns)->sortBy('date')->values()->all();
        // $returns = CentralSaleReturn::with(['products'])->where('central_sale_id', $id)->get();
        return view('studio-sale.show', [
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
        $sale = StudioSale::with(['products'])->findOrFail($id);

        $accounts = Account::where('type', '!=', 'none')->get();
        $suppliers = Supplier::all();

        // $selectedProducts = collect($sale->products)->each(function ($product) {

        // });
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

            $product['studio_stock'] = $productRow->studio_stock + ($product->pivot->quantity + $product->pivot->free);
            $product['quantity'] = $product->pivot->quantity;
            $product['free'] = $product->pivot->free;
            $product['old_taken'] = $product->pivot->quantity + $product->pivot->free;
            $product['price'] = $product->pivot->price;
        }

        $sidebarClass = 'compact';

        return view('studio-sale.edit', [
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
        $studioSale = StudioSale::findOrFail($id);
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

                $oldTakenProduct = collect($oldTakenProducts)->where('id', $product['id'])->first();

                $taken = 0;
                $oldTakenQuantity = 0;

                if ($oldTakenProduct !== null) {
                    $oldTakenQuantity = $oldTakenProduct['taken'];
                }

                $taken = ($product['quantity'] + $product['free']) - $oldTakenQuantity;

                if ($taken > $productRow->studio_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['studio_stock'] = $productRow->studio_stock;
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

            $oldAccountId = $studioSale->account_id;
            $oldNetTotal = $studioSale->net_total;
            // $studioSale->code = $studioSaleNumber;
            $studioSale->date = $request->date . ' ' . date('H:i:s');
            $studioSale->total_weight = $request->total_weight;
            $studioSale->discount = $this->clearThousandFormat($request->discount);
            $studioSale->discount_type = $request->discount_type;
            $studioSale->subtotal = $this->clearThousandFormat($request->subtotal);
            $studioSale->shipping_cost = $this->clearThousandFormat($request->shipping_cost);
            $studioSale->other_cost = $this->clearThousandFormat($request->other_cost);
            $studioSale->detail_other_cost = $request->detail_other_cost;
            $studioSale->net_total = $this->clearThousandFormat($request->net_total);
            $studioSale->payment_amount = $this->clearThousandFormat($request->pay_amount);
            $studioSale->payment_method = $request->payment_method;
            $studioSale->account_id = $request->account_id;
            $studioSale->updated_by = Auth::id();


            $studioSale->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['studio_stock'],
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

            $studioSale->products()->detach();
            $studioSale->products()->attach($keyedProducts);

            foreach ($oldTakenProducts as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->studio_stock = $productRow->studio_stock + $product['taken'];
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

                $productRow->studio_stock = $productRow->studio_stock - ($product['quantity'] + $product['free']);
                $productRow->save();
            }

            AccountTransaction::where('table_name', 'studio_sales')->where('table_id', $studioSale->id)->delete();

            $initTransactions = collect($studioSale->studioSaleTransactions)->where('is_init', 1)->pluck('id');
            if (count($initTransactions) > 0) {
                StudioSaleTransaction::query()->whereIn('id', $initTransactions)->delete();
                $studioSale->studioSaleTransactions()->wherePivotIn('studio_sale_transaction_id', $initTransactions)->detach();
            }

            if ($request->payment_method !== 'hutang') {
                $date = $request->date;
                $transactionsByCurrentDateCount = StudioSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $studioSale->id;
                $transactionNumber = 'SST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $transactionAmount = $paymentAmount > $netTotal ? $netTotal : $paymentAmount;
                // $amount = $this->clearThousandFormat($transactionAmount);

                $transaction = new StudioSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->account_id;
                $transaction->amount = $transactionAmount;
                $transaction->payment_method = $request->payment_method;
                $transaction->account_type = 'in';
                $transaction->is_init = 1;

                $transaction->save();

                $transaction->studioSales()->attach([
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
                        $accountTransaction->note = "Penjualan studio No. " . $studioSale->code;
                        $accountTransaction->date = $request->date;
                        $accountTransaction->table_name = 'studio_sales';
                        $accountTransaction->table_id = $studioSale->id;

                        $accountTransaction->save();
                    }

                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $netTotal - $paymentAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan studio No. " . $studioSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'studio_sales';
                    $accountTransaction->table_id = $studioSale->id;

                    $accountTransaction->save();
                } else {
                    // Account Transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->account_id;
                    $accountTransaction->amount = $netTotal;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Penjualan studio No. " . $studioSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'studio_sales';
                    $accountTransaction->table_id = $studioSale->id;

                    $accountTransaction->save();
                }
            } else {
                // Jika Hutang
                // Account Transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('piutang', 0);
                $accountTransaction->amount = $netTotal;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Penjualan studio No. " . $studioSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $studioSale->id;

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
                $accountTransaction->note = "Diskon penjualan studio No. " . $studioSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $studioSale->id;
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
                $accountTransaction->note = "Biaya kirim penjualan studio No. " . $studioSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'studio_sales';
                $accountTransaction->table_id = $studioSale->id;
                $accountTransaction->save();
            }
            // --------

            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $studioSale,
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
        $sale = StudioSale::findOrFail($id);
        $products = $sale->products;

        // Update Stock
        $saleReturns = StudioSaleReturn::with(['products'])
            ->where('studio_sale_id', $id)
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

        DB::beginTransaction();

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                $returnedQuantity = 0;
                // $productReturn = collect($saleReturns)->where('id', $product['id'])->first();
                // if ($productReturn !== null) {
                //     $returnedQuantity = $productReturn['returned_quantity'];
                // }

                // $productRow->studio_stock = $productRow->studio_stock + ($product->pivot->quantity - $returnedQuantity) + $product->pivot->free;
                $wrongQuantity = 0;
                $defectiveQuantity = 0;
                $productReturn = collect($saleReturns)->where('id', $product['id'])->first();
                if ($productReturn !== null) {
                    // $returnedQuantity = $productReturn['returned_quantity'];
                    $wrongQuantity = $productReturn['wrong_quantity'];
                    $defectiveQuantity = $productReturn['defective_quantity'];
                }

                $productRow->bad_stock = $productRow->bad_stock - $defectiveQuantity;
                $productRow->studio_stock = $productRow->studio_stock + ($product->pivot->quantity - ($wrongQuantity + $defectiveQuantity)) + $product->pivot->free;
                $productRow->save();
            }

            // Delete Related Return
            StudioSaleReturn::query()->where('studio_sale_id', $id)->delete();

            // Detach Product From Intermediate Table
            $sale->products()->detach();

            // Delete studio sale
            $studioSaleTransactionIds = collect($sale->studioSaleTransactions)->pluck('id')->all();
            // Delete sale transaction account transaction 
            if (count($studioSaleTransactionIds) > 0) {
                DB::table('studio_sale_transactions')->whereIn('id', $studioSaleTransactionIds)->delete();
                AccountTransaction::where('table_name', 'studio_sale_transactions')->whereIn('table_id', $studioSaleTransactionIds)->delete();
            }

            // Account Transaction
            AccountTransaction::where('table_name', 'studio_sales')->where('table_id', $sale->id)->delete();

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
        $sale = StudioSale::with(['products'])->findOrFail($id);
        $accounts = Account::where('type', '!=', 'none')->get();

        $saleReturnProducts = StudioSaleReturn::with(['products'])
            ->where('studio_sale_id', $sale->id)
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

        return view('studio-sale.return', [
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
        $sale = StudioSale::with(['products'])->findOrFail($id);

        $data = [
            'sale' => $sale,
        ];

        // $pdf = PDF::loadView('studio-sale.print-80mm', $data, [], [
        //     'format' => 'A5',
        // ]);
        // return $pdf->stream($sale->code . '.pdf');
        return view('studio-sale.print-80mm', $data);
    }

    public function report(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new StudioSaleDetailExport($request->all()), 'Studio Sales Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
    }

    public function datatableStudioSales()
    {
        $studioSales = StudioSale::with(['products', 'createdBy'])->select('studio_sales.*');
        return DataTables::of($studioSales)
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
                        <a href="/studio-sale/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/studio-sale/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';

                $button .= '<a href="/studio-sale/return/' . $row->id . '"><em class="icon fas fa-undo"></em><span>Retur</span></a>';
                $button .= '<a href="/studio-sale/print/' . $row->id . '" target="_blank"><em class="icon fas fa-print"></em><span>Cetak</span></a>';


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
