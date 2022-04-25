<?php

namespace App\Http\Controllers;

use App\Exports\CentralSaleByCustomerDetailExport;
use App\Exports\CentralSaleByCustomerSummaryExport;
use App\Exports\CentralSaleByProductDetailExport;
use App\Exports\CentralSaleByProductSummaryExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralSale;
use App\Models\CentralSaleReturn;
use App\Models\CentralSaleTransaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use PDF;

class CentralSaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shipments = Shipment::all();
        $centralSale = CentralSale::all();
        return view('central-sale.index', [
            'centralSale' => $centralSale,
            'shipment' => $shipments,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        $accounts = Account::where('type', '!=', 'none')->get();
        $maxid = DB::table('central_sales')->max('id');
        $code = "SO/" . date('dmy') . "/" . sprintf('%04d', $maxid + 1);

        $sidebarClass = 'compact';

        return view('central-sale.create', [
            'code' => $code,
            'customer' => $customers,
            'shipment' => $shipments,
            'accounts' => $accounts,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOld(Request $request)
    {
        $centralSale = new CentralSale;
        $centralSale->code = $request->code;
        $centralSale->date = $request->date . ' ' . date('H:i:s');
        $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
        $centralSale->customer_id = $request->customer_id;
        $centralSale->shipment_id = $request->shipment_id;
        $centralSale->debt = $request->debt;
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
        $centralSale->discount_type = $request->discount_type;
        $centralSale->subtotal = $request->subtotal;
        $centralSale->shipping_cost = $request->shipping_cost;
        $centralSale->other_cost = $request->other_cost;
        $centralSale->detail_other_cost = $request->detail_other_cost;
        $centralSale->deposit_customer = $request->deposit_customer;
        $centralSale->net_total = $request->net_total;
        $centralSale->receipt_1 = $request->receipt_1;
        $centralSale->receive_1 = $request->receive_1;
        $centralSale->receipt_2 = $request->receipt_2;
        $centralSale->receive_2 = $request->receive_2;
        $centralSale->recipient = $request->recipient;
        $centralSale->payment_amount = $request->payment_amount;
        $centralSale->remaining_payment = $request->remaining_payment;
        $centralSale->address_recipient = $request->address_recipient;
        $centralSale->detail = $request->detail;
        $centralSale->created_by = Auth::id();
        $products = $request->selected_products;

        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                // let taken = Number(booked) + Number(quantity) + Number(free);
                // console.log(taken);
                // if (taken > Number(central_stock)) {
                //     const maxAvailable = Number(central_stock) - (Number(booked) + Number(quantity));
                //     // console.log(central_stock, booked, quantity, maxAvailable);
                //     product.free = maxAvailable;
                //     product.subTotal = this.getProductSubtotal(product);
                // }

                $taken = $productRow->booked + $product['quantity'] + $product['free'];
                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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

        try {
            $centralSale->save();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'booked' => $item['booked'],
                    'price' => str_replace(".", "", $item['price']),
                    'quantity' => $item['quantity'],
                    'free' => $item['free'],
                    'amount' => $item['subTotal'],
                    'editable' => $item['editable'] == true ? 1 : 0,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $centralSale->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                // $productRow->agent_price = str_replace(".", "", $product['price']);
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
            ]);
        } catch (Exception $e) {
            $centralSale->products()->detach();
            $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $products = $request->selected_products;

        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                // let taken = Number(booked) + Number(quantity) + Number(free);
                // console.log(taken);
                // if (taken > Number(central_stock)) {
                //     const maxAvailable = Number(central_stock) - (Number(booked) + Number(quantity));
                //     // console.log(central_stock, booked, quantity, maxAvailable);
                //     product.free = maxAvailable;
                //     product.subTotal = this.getProductSubtotal(product);
                // }

                $taken = $productRow->booked + $product['quantity'] + $product['free'];
                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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
            $centralSale = new CentralSale;
            $centralSale->code = $request->code;
            $centralSale->date = $request->date . ' ' . date('H:i:s');
            $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
            $centralSale->customer_id = $request->customer_id;
            $centralSale->shipment_id = $request->shipment_id;
            $centralSale->debt = $request->debt;
            $centralSale->total_weight = $request->total_weight;
            $centralSale->total_cost = $request->total_cost;
            $centralSale->discount = $request->discount;
            $centralSale->discount_type = $request->discount_type;
            $centralSale->subtotal = $request->subtotal;
            $centralSale->shipping_cost = $request->shipping_cost;
            $centralSale->other_cost = $request->other_cost;
            $centralSale->detail_other_cost = $request->detail_other_cost;
            $centralSale->deposit_customer = $request->deposit_customer;
            $centralSale->net_total = $request->net_total;
            $centralSale->receipt_1 = $request->receipt_1;
            $centralSale->receive_1 = $request->receive_1;
            $centralSale->receipt_2 = $request->receipt_2;
            $centralSale->receive_2 = $request->receive_2;
            $centralSale->recipient = $request->recipient;
            $centralSale->payment_amount = $request->payment_amount;
            $centralSale->remaining_payment = $request->remaining_payment;
            $centralSale->address_recipient = $request->address_recipient;
            $centralSale->detail = $request->detail;
            $centralSale->created_by = Auth::id();

            $centralSale->save();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['central_stock'],
                        'booked' => $item['booked'],
                        'price' => str_replace(".", "", $item['price']),
                        'quantity' => $item['quantity'],
                        'free' => $item['free'],
                        'amount' => $item['subTotal'],
                        'editable' => $item['editable'] == true ? 1 : 0,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            $centralSale->products()->attach($keyedProducts);

            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                // $productRow->agent_price = str_replace(".", "", $product['price']);
                $productRow->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
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
        $centralSale = CentralSale::with('products.productCategory')->findOrFail($id);
        // return collect($centralSale->centralSaleTransactions)->pluck('id')->all();
        $transactions = collect($centralSale->centralSaleTransactions)->sortBy('date')->values()->all();
        $returns = collect($centralSale->centralSaleReturns)->sortBy('date')->values()->all();
        // $returns = CentralSaleReturn::with(['products'])->where('central_sale_id', $id)->get();
        return view('central-sale.show', [
            'centralSale' => $centralSale,
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
        $centralSale = CentralSale::with(['products'])->findOrFail($id);

        // $initTransactions = collect($centralSale->centralSaleTransactions)->where('is_init', 1)->pluck('id');
        // return $initTransactions;

        // if ($centralSale->status !== 'pending') {
        //     return redirect('/central-sale');
        // }

        $customers = Customer::all();
        $shipments = Shipment::all();
        $accounts = Account::where('type', '!=', 'none')->get();

        $oldTakenProducts = collect($centralSale->products)->groupBy('id')->map(function ($group, $productId) {
            return [
                'id' => $productId,
                'taken' => collect($group)->sum(function ($product) {
                    return $product->pivot->quantity + $product->pivot->free;
                }),
            ];
        })->values()->all();

        $selectedProducts = collect($centralSale->products)->each(function ($product) use ($centralSale) {
            // $product['stock'] = $product->pivot->stock;
            $booked = $product->booked - ($product->pivot->quantity + $product->pivot->free);
            if ($centralSale->status == 'approved') {
                $booked = $product->booked;
                $product->central_stock = $product->central_stock + ($product->pivot->quantity + $product->pivot->free);
            }
            $product['booked'] = $booked;
            $product['quantity'] = $product->pivot->quantity;
            $product['price'] = $product->pivot->price;
            $product['free'] = $product->pivot->free;
            $product['subTotal'] = $product->pivot->amount;
            $product['editable'] = $product->pivot->editable == 1 ? true : false;
            $product['old_booking_amount'] = $product->pivot->quantity + $product->pivot->free;
            $product['old_quantity_amount'] = $product->pivot->quantity + $product->pivot->free;
            // $product['cause'] = 'defective';
        });

        $sidebarClass = 'compact';

        if ($centralSale->status == 'approved') {
            return view('central-sale.edit-approved', [
                'central_sale' => $centralSale,
                'customers' => $customers,
                'accounts' => $accounts,
                'shipments' => $shipments,
                'selected_products' => $selectedProducts,
                'old_taken_products' => $oldTakenProducts,
                'sidebar_class' => $sidebarClass,
            ]);
        }

        return view('central-sale.edit', [
            'central_sale' => $centralSale,
            'customers' => $customers,
            'accounts' => $accounts,
            'shipments' => $shipments,
            'selected_products' => $selectedProducts,
            'old_taken_products' => $oldTakenProducts,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOld(Request $request, $id)
    {
        $centralSale = CentralSale::findOrFail($id);
        // $centralSale->code = $request->code;
        $centralSale->date = date_format(date_create($request->date), "Y-m-d") . ' ' . date('H:i:s');
        $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
        $centralSale->customer_id = $request->customer_id;
        $centralSale->shipment_id = $request->shipment_id;
        $centralSale->debt = $request->debt;
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
        $centralSale->discount_type = $request->discount_type;
        $centralSale->subtotal = $request->subtotal;
        $centralSale->shipping_cost = $request->shipping_cost;
        $centralSale->other_cost = $request->other_cost;
        $centralSale->detail_other_cost = $request->detail_other_cost;
        $centralSale->deposit_customer = $request->deposit_customer;
        $centralSale->net_total = $request->net_total;
        $centralSale->receipt_1 = $request->receipt_1;
        $centralSale->receive_1 = $request->receive_1;
        $centralSale->receipt_2 = $request->receipt_2;
        $centralSale->receive_2 = $request->receive_2;
        $centralSale->recipient = $request->recipient;
        $centralSale->payment_amount = $request->payment_amount;
        $centralSale->remaining_payment = $request->remaining_payment;
        $centralSale->address_recipient = $request->address_recipient;
        $centralSale->detail = $request->detail;
        $products = $request->selected_products;

        // Check Available Stock
        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                $taken = $productRow->booked + $product['quantity'] + $product['free'];

                if (array_key_exists('old_booking_amount', $product)) {
                    $taken = ($productRow->booked - $product['old_booking_amount']) + $product['quantity'] + $product['free'];
                    // $productRow->booked = $productRow->booked - $product['old_booking_amount'];
                }

                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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

        // Delete all Conjuction Data
        try {
            $centralSale->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error detaching products',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Mapping Conjunctions Data
        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'booked' => $item['booked'],
                    'price' => str_replace(".", "", $item['price']),
                    'quantity' => $item['quantity'],
                    'free' => $item['free'],
                    'amount' => $item['subTotal'],
                    'editable' => $item['editable'] == true ? 1 : 0,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        // Save Conjuction Data
        try {
            $centralSale->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error attaching product',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Update Stock On Product
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                // if (array_key_exists('old_booking_amount', $product)) {
                //     $finalBooked = $productRow->booked - $product['old_booking_amount'];
                //     $productRow->booked = $finalBooked < 0 ? 0 : $finalBooked;
                // }
                if ($centralSale->status == 'pending') {
                    if (array_key_exists('old_booking_amount', $product)) {
                        $productRow->booked = $productRow->booked - $product['old_booking_amount'];
                        $productRow->booked = $productRow->booked + ($product['quantity'] + $product['free']);
                    }
                } else if ($centralSale->status == 'approved') {
                    $productRow->central_stock = $productRow->central_stock + $product['old_quantity_amount'];
                    $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);
                }

                $productRow->ws_price = str_replace(".", "", $product['price']);

                $productRow->save();
            }
        } catch (Exception $e) {
            // $centralSale->products()->detach();
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error update products',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Detach init transaction
        if ($centralSale->status == 'approved') {
            try {
                $initTransactions = collect($centralSale->centralSaleTransactions)->where('is_init', 1)->pluck('id');
                if (count($initTransactions) > 0) {
                    CentralSaleTransaction::query()->whereIn('id', $initTransactions)->delete();
                    $centralSale->centralSaleTransactions()->wherePivotIn('central_sale_transaction_id', $initTransactions)->detach();
                }
            } catch (Exception $e) {
                // $centralSale->delete();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            try {
                AccountTransaction::where('table_name', 'central_sales')->where('table_id', $centralSale->id)->delete();
            } catch (Exception $e) {
                // $centralSale->delete();
                return response()->json([
                    'message' => 'Internal error while deleting account transaction',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            if (($request->receipt_1 !== '' && $request->receipt_1 !== null) && ($request->receive_1 !== '' && $request->receive_1 !== null)) {
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $centralSale->id;
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $amount = $this->clearThousandFormat($request->receive_1);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->receipt_1;
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $amount;
                // $transaction->payment_method = $request->payment_method;
                $transaction->payment_method = 'transfer';
                $transaction->is_init = 1;
                // $transaction->note = $request->note;

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

                try {
                    $transaction->centralSales()->attach([
                        $saleId => [
                            'amount' => $amount,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);
                    // return response()->json([
                    //     'message' => 'Data has been saved',
                    //     'code' => 200,
                    //     'error' => false,
                    //     'data' => $transaction,
                    // ]);
                } catch (Exception $e) {
                    $transaction->delete();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }

                try {
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->receipt_1;
                    $accountTransaction->amount = $amount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;

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

            if (($request->receipt_2 !== '' && $request->receipt_2 !== null) && ($request->receive_2 !== '' && $request->receive_2 !== null)) {
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $centralSale->id;
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $amount = $this->clearThousandFormat($request->receive_2);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->receipt_2;
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $amount;
                // $transaction->payment_method = $request->payment_method;
                $transaction->payment_method = 'transfer';
                $transaction->is_init = 1;
                // $transaction->note = $request->note;

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

                try {
                    $transaction->centralSales()->attach([
                        $saleId => [
                            'amount' => $amount,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);
                    // return response()->json([
                    //     'message' => 'Data has been saved',
                    //     'code' => 200,
                    //     'error' => false,
                    //     'data' => $transaction,
                    // ]);
                } catch (Exception $e) {
                    $transaction->delete();
                    return response()->json([
                        'message' => 'Internal error',
                        'code' => 500,
                        'error' => true,
                        'errors' => $e,
                    ], 500);
                }

                try {
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->receipt_2;
                    $accountTransaction->amount = $amount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;

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

            $receiveAmount1 = $this->clearThousandFormat($request->receive_1);
            $receiveAmount2 = $this->clearThousandFormat($request->receive_2);
            $totalReceiveAmount = (int) $receiveAmount1 + (int) $receiveAmount2;
            if ($totalReceiveAmount < $request->net_total) {
                try {
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = 2;
                    $accountTransaction->amount = $request->net_total - $totalReceiveAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Piutang penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;

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
        }

        // Save Main Data
        try {
            $centralSale->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Save Transaction
        return response()->json([
            'message' => 'Data has been saved',
            'code' => 200,
            'error' => false,
            'data' => $centralSale,
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
        $products = $request->selected_products;
        $oldTakenProducts = $request->old_taken_products;
        $centralSale = CentralSale::findOrFail($id);
        // Check Available Stock
        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                $oldTakenProduct = collect($oldTakenProducts)->where('id', $product['id'])->first();

                $taken = 0;
                $oldTakenQuantity = 0;

                if ($oldTakenProduct !== null) {
                    $oldTakenQuantity = $oldTakenProduct['taken'];
                }

                if ($centralSale->status == 'pending') {
                    $taken = ($productRow->booked - $oldTakenQuantity) + $product['quantity'] + $product['free'];
                } else if ($centralSale->status == 'approved') {
                    $taken = ($product['quantity'] + $product['free']) - $oldTakenQuantity;
                }

                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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
            // $centralSale->code = $request->code;
            $centralSale->date = date_format(date_create($request->date), "Y-m-d") . ' ' . date('H:i:s');
            $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
            $centralSale->customer_id = $request->customer_id;
            $centralSale->shipment_id = $request->shipment_id;
            $centralSale->debt = $request->debt;
            $centralSale->total_weight = $request->total_weight;
            $centralSale->total_cost = $request->total_cost;
            $centralSale->discount = $request->discount;
            $centralSale->discount_type = $request->discount_type;
            $centralSale->subtotal = $request->subtotal;
            $centralSale->shipping_cost = $request->shipping_cost;
            $centralSale->other_cost = $request->other_cost;
            $centralSale->detail_other_cost = $request->detail_other_cost;
            $centralSale->deposit_customer = $request->deposit_customer;
            $centralSale->net_total = $request->net_total;
            $centralSale->receipt_1 = $request->receipt_1;
            $centralSale->receive_1 = $request->receive_1;
            $centralSale->receipt_2 = $request->receipt_2;
            $centralSale->receive_2 = $request->receive_2;
            $centralSale->recipient = $request->recipient;
            $centralSale->payment_amount = $request->payment_amount;
            $centralSale->remaining_payment = $request->remaining_payment;
            $centralSale->address_recipient = $request->address_recipient;
            $centralSale->detail = $request->detail;
            $centralSale->save();

            $centralSale->products()->detach();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['central_stock'],
                        'booked' => $item['booked'],
                        'price' => str_replace(".", "", $item['price']),
                        'quantity' => $item['quantity'],
                        'free' => $item['free'],
                        'amount' => $item['subTotal'],
                        'editable' => $item['editable'] == true ? 1 : 0,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            $centralSale->products()->attach($keyedProducts);

            foreach ($oldTakenProducts as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                if ($centralSale->status == 'pending') {
                    $productRow->booked = $productRow->booked - $product['taken'];
                } else if ($centralSale->status == 'approved') {
                    $productRow->central_stock = $productRow->central_stock + $product['taken'];
                }
                $productRow->save();
            }


            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                if ($centralSale->status == 'pending') {
                    $productRow->booked = $productRow->booked + ($product['quantity'] + $product['free']);
                } else if ($centralSale->status == 'approved') {
                    $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);
                }
                // if ($centralSale->status == 'pending') {
                //     if (array_key_exists('old_booking_amount', $product)) {
                //         $productRow->booked = $productRow->booked - $product['old_booking_amount'];
                //         $productRow->booked = $productRow->booked + ($product['quantity'] + $product['free']);
                //     }
                // } else if ($centralSale->status == 'approved') {
                //     $productRow->central_stock = $productRow->central_stock + $product['old_quantity_amount'];
                //     $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);
                // }
                // $productRow->ws_price = str_replace(".", "", $product['price']);
                $productRow->save();
            }

            // Detach init transaction
            if ($centralSale->status == 'approved') {
                // Delete Init Transaction
                $initTransactions = collect($centralSale->centralSaleTransactions)->where('is_init', 1)->pluck('id');
                if (count($initTransactions) > 0) {
                    CentralSaleTransaction::query()->whereIn('id', $initTransactions)->delete();
                    $centralSale->centralSaleTransactions()->wherePivotIn('central_sale_transaction_id', $initTransactions)->detach();
                }

                AccountTransaction::where('table_name', 'central_sales')->where('table_id', $centralSale->id)->delete();

                if (($request->receipt_1 !== '' && $request->receipt_1 !== null) && ($request->receive_1 !== '' && $request->receive_1 !== null)) {
                    $date = $request->date;
                    $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                    $saleId = $centralSale->id;
                    $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                    $amount = $this->clearThousandFormat($request->receive_1);

                    $transaction = new CentralSaleTransaction;
                    $transaction->code = $transactionNumber;
                    $transaction->date = $date;
                    $transaction->account_id = $request->receipt_1;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->amount = $amount;
                    // $transaction->payment_method = $request->payment_method;
                    $transaction->payment_method = 'transfer';
                    $transaction->is_init = 1;
                    // $transaction->note = $request->note;

                    $transaction->save();

                    $transaction->centralSales()->attach([
                        $saleId => [
                            'amount' => $amount,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);

                    // -------- Save to account transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->receipt_1;
                    $accountTransaction->amount = $amount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;
                    $accountTransaction->save();
                    // --------
                }

                if (($request->receipt_2 !== '' && $request->receipt_2 !== null) && ($request->receive_2 !== '' && $request->receive_2 !== null)) {
                    $date = $request->date;
                    $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                    $saleId = $centralSale->id;
                    $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                    $amount = $this->clearThousandFormat($request->receive_2);

                    $transaction = new CentralSaleTransaction;
                    $transaction->code = $transactionNumber;
                    $transaction->date = $date;
                    $transaction->account_id = $request->receipt_2;
                    $transaction->customer_id = $request->customer_id;
                    $transaction->amount = $amount;
                    // $transaction->payment_method = $request->payment_method;
                    $transaction->payment_method = 'transfer';
                    $transaction->is_init = 1;
                    // $transaction->note = $request->note;

                    $transaction->save();

                    $transaction->centralSales()->attach([
                        $saleId => [
                            'amount' => $amount,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ]
                    ]);

                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = $request->receipt_2;
                    $accountTransaction->amount = $amount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;

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
                    $accountTransaction->note = "Diskon penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;
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
                    $accountTransaction->note = "Biaya kirim penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;
                    $accountTransaction->save();
                }
                // --------

                $receiveAmount1 = $this->clearThousandFormat($request->receive_1);
                $receiveAmount2 = $this->clearThousandFormat($request->receive_2);
                $totalReceiveAmount = (int) $receiveAmount1 + (int) $receiveAmount2;
                if ($totalReceiveAmount < $request->net_total) {

                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->account_id = config('accounts.piutang', 0);
                    $accountTransaction->amount = $request->net_total - $totalReceiveAmount;
                    $accountTransaction->type = "in";
                    $accountTransaction->note = "Piutang penjualan pusat No. " . $centralSale->code;
                    $accountTransaction->date = $request->date;
                    $accountTransaction->table_name = 'central_sales';
                    $accountTransaction->table_id = $centralSale->id;

                    $accountTransaction->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error detaching products',
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
    public function destroyOld($id)
    {
        $sale = CentralSale::findOrFail($id);
        $products = $sale->products;

        $saleReturns = CentralSaleReturn::with(['products'])
            ->where('central_sale_id', $id)
            ->get()
            ->flatMap(function ($return) {
                return $return->products;
            })
            ->groupBy('id')
            ->map(function ($products, $productId) {
                $returnedQuantity = collect($products)->sum(function ($product) {
                    return $product->pivot->quantity;
                });

                return [
                    'id' => $productId,
                    'returned_quantity' => $returnedQuantity,
                ];
            })->values()->all();

        // return collect($saleReturns)->where('id', 1)->first();

        // Update Stock
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                $returnedQuantity = 0;
                $productReturn = collect($saleReturns)->where('id', $product['id'])->first();
                if ($productReturn !== null) {
                    $returnedQuantity = $productReturn['returned_quantity'];
                }

                if ($sale->status == 'pending') {
                    $productRow->booked = $productRow->booked - ($product->pivot->quantity - $returnedQuantity) + $product->pivot->free;
                } else if ($sale->status == 'approved') {
                    $productRow->central_stock = $productRow->central_stock + ($product->pivot->quantity - $returnedQuantity) + $product->pivot->free;
                }
                $productRow->save();
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Delete Related Return
        try {
            CentralSaleReturn::query()->where('central_sale_id', $id)->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error detaching products',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Detach Product From Intermediate Table
        try {
            $sale->products()->detach();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error detaching products',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        if ($sale->status == 'approved') {
            // Delete and detach transaction
            try {
                // $initTransactions = collect($sale->centralSaleTransactions)->pluck('id');
                // if (count($initTransactions) > 0) {
                //     CentralSaleTransaction::query()->whereIn('id', $initTransactions)->delete();
                //     $sale->centralSaleTransactions()->detach();
                // }
                $sale->centralSaleTransactions()->detach();
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            try {
                AccountTransaction::where('table_name', 'central_sales')->where('table_id', $sale->id)->delete();
            } catch (Exception $e) {
                // $centralSale->delete();
                return response()->json([
                    'message' => 'Internal error while deleting account transaction',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }
        }

        // Delete Main Data
        try {
            $sale->delete();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        return response()->json([
            'message' => 'Data has been deleted',
            'code' => 200,
            'error' => false,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = CentralSale::findOrFail($id);

        // $centralSaleTransactionIds = collect($sale->centralSaleTransactions)->pluck('id')->all();
        // return  gettype($centralSaleTransactionIds);
        $products = $sale->products;

        $saleReturns = CentralSaleReturn::with(['products'])
            ->where('central_sale_id', $id)
            ->get()
            ->flatMap(function ($return) {
                return $return->products;
            })
            ->groupBy('id')
            ->map(function ($products, $productId) {
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

        // TODO: Deletion steps
        // * Update / return stock to the beginning
        // * Delete related return
        // ? Delete return transaction
        // ? Detach return & return transaction
        // ? [account transaction] Remove piutang from return 
        // * Detach product
        // ? if approved: 
        // ? Delete all transaction, 
        // ? detach
        // ? delete from account transaction

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

                if ($sale->status == 'pending') {
                    $productRow->bad_stock = $productRow->bad_stock - $defectiveQuantity;
                    $productRow->booked = $productRow->booked - ($product->pivot->quantity - ($wrongQuantity + $defectiveQuantity)) + $product->pivot->free;
                } else if ($sale->status == 'approved') {
                    $productRow->bad_stock = $productRow->bad_stock - $defectiveQuantity;
                    $productRow->central_stock = $productRow->central_stock + ($product->pivot->quantity - ($wrongQuantity + $defectiveQuantity)) + $product->pivot->free;
                }
                // if ($sale->status == 'pending') {
                //     $productRow->booked = $productRow->booked - ($product->pivot->quantity) + $product->pivot->free;
                // } else if ($sale->status == 'approved') {
                //     $productRow->central_stock = $productRow->central_stock + ($product->pivot->quantity) + $product->pivot->free;
                // }
                $productRow->save();
            }

            $centralSaleReturns = CentralSaleReturn::query()->where('central_sale_id', $id)->get()->pluck('id');
            // Delete Related Return
            CentralSaleReturn::query()->where('central_sale_id', $id)->delete();
            // Get return transaction
            $returnPivotTable = DB::table('central_sale_return_central_sale_return_transaction')->whereIn('central_sale_return_id', $centralSaleReturns);

            $centralSaleReturnTransactionIds = $returnPivotTable->get()->pluck('central_sale_return_transaction_id');
            // Delete/detach return return transaction pivot table
            $returnPivotTable->delete();

            // Delete central sale return transactions
            if (count($centralSaleReturnTransactionIds) > 0) {
                DB::table('central_sale_return_transactions')->whereIn('id', $centralSaleReturnTransactionIds)->delete();
            }

            // Detach Product From Intermediate Table
            $sale->products()->detach();

            // Delete sale transaction
            $centralSaleTransactionIds = collect($sale->centralSaleTransactions)->pluck('id')->all();
            // Delete sale transaction account transaction 
            if (count($centralSaleTransactionIds) > 0) {
                DB::table('central_sale_transactions')->whereIn('id', $centralSaleTransactionIds)->delete();
                AccountTransaction::where('table_name', 'central_sale_transactions')->whereIn('table_id', $centralSaleTransactionIds)->delete();
            }

            if ($sale->status == 'approved') {
                // Delete and detach transaction
                $sale->centralSaleTransactions()->detach();
                // Delete sale account transaction
                AccountTransaction::where('table_name', 'central_sales')->where('table_id', $sale->id)->delete();
            }

            // Delete piutang transaction from return
            AccountTransaction::where('table_name', 'central_sale_returns')->whereIn('table_id', $centralSaleReturns)->delete();

            // Delete Main Data
            $sale->delete();

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

    public function approval($id)
    {
        $centralSale = CentralSale::with(['products'])->findOrFail($id);

        if ($centralSale->status !== 'pending') {
            return redirect('/central-sale');
        }

        $customers = Customer::all();
        $shipments = Shipment::all();
        $accounts = Account::where('type', '!=', 'none')->get();

        $oldTakenProducts = collect($centralSale->products)->groupBy('id')->map(function ($group, $productId) {
            return [
                'id' => $productId,
                'taken' => collect($group)->sum(function ($product) {
                    return $product->pivot->quantity + $product->pivot->free;
                }),
            ];
        })->values()->all();

        $selectedProducts = collect($centralSale->products)->each(function ($product) use ($centralSale) {
            // $booked = $product->booked - ($product->pivot->quantity + $product->pivot->free);
            // if ($centralSale->status == 'approved') {
            //     $booked = $product->booked;
            //     $product->central_stock = $product->central_stock + ($product->pivot->quantity + $product->pivot->free);
            // }
            // $product['stock'] = $product->pivot->stock;
            $product['booked'] = $product->booked - ($product->pivot->quantity + $product->pivot->free);
            $product['quantity'] = $product->pivot->quantity;
            $product['price'] = $product->pivot->price;
            $product['free'] = $product->pivot->free;
            $product['subTotal'] = $product->pivot->amount;
            $product['editable'] = $product->pivot->editable == 1 ? true : false;
            $product['old_booking_amount'] = $product->pivot->quantity + $product->pivot->free;
            // $product['cause'] = 'defective';
        });

        $sidebarClass = 'compact';

        return view('central-sale.approval', [
            'central_sale' => $centralSale,
            'customers' => $customers,
            'accounts' => $accounts,
            'shipments' => $shipments,
            'old_taken_products' => $oldTakenProducts,
            'selected_products' => $selectedProducts,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    public function approveOld(Request $request, $id)
    {
        $centralSale = CentralSale::findOrFail($id);
        // $centralSale->code = $request->code;
        $centralSale->date = date_format(date_create($request->date), "Y-m-d") . ' ' . date('H:i:s');
        $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
        $centralSale->customer_id = $request->customer_id;
        $centralSale->shipment_id = $request->shipment_id;
        $centralSale->debt = $request->debt;
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
        $centralSale->discount_type = $request->discount_type;
        $centralSale->subtotal = $request->subtotal;
        $centralSale->shipping_cost = $request->shipping_cost;
        $centralSale->other_cost = $request->other_cost;
        $centralSale->detail_other_cost = $request->detail_other_cost;
        $centralSale->deposit_customer = $request->deposit_customer;
        $centralSale->net_total = $request->net_total;
        $centralSale->receipt_1 = $request->receipt_1;
        $centralSale->receive_1 = $request->receive_1;
        $centralSale->receipt_2 = $request->receipt_2;
        $centralSale->receive_2 = $request->receive_2;
        $centralSale->recipient = $request->recipient;
        $centralSale->payment_amount = $request->payment_amount;
        $centralSale->remaining_payment = $request->remaining_payment;
        $centralSale->address_recipient = $request->address_recipient;
        $centralSale->detail = $request->detail;
        $products = $request->selected_products;

        // Check Available Stock
        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                $taken = $productRow->booked + $product['quantity'] + $product['free'];

                if (array_key_exists('old_booking_amount', $product)) {
                    $taken = ($productRow->booked - $product['old_booking_amount']) + $product['quantity'] + $product['free'];
                    // $productRow->booked = $productRow->booked - $product['old_booking_amount'];
                }

                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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

        // Delete all Conjuction Data
        try {
            $centralSale->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Mapping Conjunctions Data
        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'booked' => $item['booked'],
                    'price' => str_replace(".", "", $item['price']),
                    'quantity' => $item['quantity'],
                    'free' => $item['free'],
                    'amount' => $item['subTotal'],
                    'editable' => $item['editable'] == true ? 1 : 0,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        // Save Conjuction Data
        try {
            $centralSale->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Update Stock On Product
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                if (array_key_exists('old_booking_amount', $product)) {
                    $finalBooked = $productRow->booked - $product['old_booking_amount'];
                    $productRow->booked = $finalBooked < 0 ? 0 : $finalBooked;
                }

                $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);

                $productRow->ws_price = str_replace(".", "", $product['price']);

                $productRow->save();
            }
        } catch (Exception $e) {
            $centralSale->products()->detach();
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Save Main Data
        try {
            $centralSale->status = 'approved';

            $centralSale->save();

            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $centralSale,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Save Transaction

        if (($request->receipt_1 !== '' && $request->receipt_1 !== null) && ($request->receive_1 !== '' && $request->receive_1 !== null)) {
            $date = $request->date;
            $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            $saleId = $centralSale->id;
            $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
            $amount = $this->clearThousandFormat($request->receive_1);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $date;
            $transaction->account_id = $request->receipt_1;
            $transaction->account_type = 'in';
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            // $transaction->payment_method = $request->payment_method;
            $transaction->payment_method = 'transfer';
            $transaction->is_init = 1;
            // $transaction->note = $request->note;

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

            try {
                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $amount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);
                // return response()->json([
                //     'message' => 'Data has been saved',
                //     'code' => 200,
                //     'error' => false,
                //     'data' => $transaction,
                // ]);
            } catch (Exception $e) {
                $transaction->delete();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            // Account Transaction
            try {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = $request->receipt_1;
                $accountTransaction->amount = $amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;

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

        if (($request->receipt_2 !== '' && $request->receipt_2 !== null) && ($request->receive_2 !== '' && $request->receive_2 !== null)) {
            $date = $request->date;
            $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            $saleId = $centralSale->id;
            $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
            $amount = $this->clearThousandFormat($request->receive_2);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $date;
            $transaction->account_id = $request->receipt_2;
            $transaction->account_type = 'in';
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            // $transaction->payment_method = $request->payment_method;
            $transaction->payment_method = 'transfer';
            $transaction->is_init = 1;
            // $transaction->note = $request->note;

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

            try {
                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $amount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);
                // return response()->json([
                //     'message' => 'Data has been saved',
                //     'code' => 200,
                //     'error' => false,
                //     'data' => $transaction,
                // ]);
            } catch (Exception $e) {
                $transaction->delete();
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }

            // Account Transaction
            try {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = $request->receipt_2;
                $accountTransaction->amount = $amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;

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

        $receiveAmount1 = $this->clearThousandFormat($request->receive_1);
        $receiveAmount2 = $this->clearThousandFormat($request->receive_2);
        $totalReceiveAmount = (int) $receiveAmount1 + (int) $receiveAmount2;
        if ($totalReceiveAmount < $request->net_total) {

            try {
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = 2;
                $accountTransaction->amount = $request->net_total - $totalReceiveAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Piutang penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;

                $accountTransaction->save();
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Internal error',
                    'code' => 500,
                    'error' => true,
                    'errors' => $e,
                ], 500);
            }


            // $piutangAccount = Account::where('type', 'piutang')->first();
            // if ($piutangAccount !== null) {
            //     $accountTransaction = new AccountTransaction;
            //     $accountTransaction->account_in = $piutangAccount->id;
            //     $accountTransaction->amount = $request->net_total - $totalReceiveAmount;
            //     $accountTransaction->type = "in";
            //     $accountTransaction->note = "Piutang penjualan pusat No. " . $centralSale->code;
            //     $accountTransaction->date = $request->date;

            //     try {
            //         $accountTransaction->save();
            //     } catch (Exception $e) {
            //         return response()->json([
            //             'message' => 'Internal error',
            //             'code' => 500,
            //             'error' => true,
            //             'errors' => $e,
            //         ], 500);
            //     }
            // }

            // $date = $request->date;
            // $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            // $saleId = $centralSale->id;
            // $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

            // $transaction = new CentralSaleTransaction;
            // $transaction->code = $transactionNumber;
            // $transaction->date = $date;
            // $transaction->account_id = 2;
            // $transaction->account_type = 'in';
            // $transaction->customer_id = $request->customer_id;
            // $transaction->amount = $request->net_total - $totalReceiveAmount;
            // // $transaction->payment_method = $request->payment_method;
            // $transaction->payment_method = 'piutang';
            // // $transaction->note = $request->note;

            // try {
            //     $transaction->save();
            // } catch (Exception $e) {
            //     return response()->json([
            //         'message' => 'Internal error',
            //         'code' => 500,
            //         'error' => true,
            //         'errors' => $e,
            //     ], 500);
            // }


        }

        return response()->json([
            'message' => 'Data has been saved',
            'code' => 200,
            'error' => false,
            'data' => $centralSale,
        ]);
    }

    public function approve(Request $request, $id)
    {
        $products = $request->selected_products;
        $oldTakenProducts = $request->old_taken_products;
        $centralSale = CentralSale::findOrFail($id);
        // Check Available Stock
        try {
            $unavailableStockProductCount = 0;
            $newSelectedProducts = [];
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);

                $oldTakenProduct = collect($oldTakenProducts)->where('id', $product['id'])->first();

                $taken = 0;
                $oldTakenQuantity = 0;

                if ($oldTakenProduct !== null) {
                    $oldTakenQuantity = $oldTakenProduct['taken'];
                }

                $taken = ($productRow->booked - $oldTakenQuantity) + $product['quantity'] + $product['free'];
                // if ($centralSale->status == 'pending') {
                //     $taken = ($productRow->booked - $oldTakenQuantity) + $product['quantity'] + $product['free'];
                // } else if ($centralSale->status == 'approved') {
                //     $taken = ($product['quantity'] + $product['free']) - $oldTakenQuantity;
                // }

                if ($taken > $productRow->central_stock) {
                    // array_push($unavailableStockProductIds, $productRow);
                    $product['booked'] = $productRow->booked;
                    $product['central_stock'] = $productRow->central_stock;
                    $product['quantity'] = 1;
                    $product['free'] = 0;
                    $product['editable'] = 0;
                    $product['subTotal'] = $product['ws_price'];
                    $product['backgroundColor'] = 'bg-warning-dim';
                    array_push($newSelectedProducts, $product);
                    $unavailableStockProductCount++;
                } else {
                    array_push($newSelectedProducts, $product);
                }
                // if ($productRow == null) {
                //     continue;
                // }

                // $productRow->booked = $productRow->booked + $product['quantity'];
                // $productRow->save();
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
            // $centralSale->code = $request->code;
            $centralSale->date = date_format(date_create($request->date), "Y-m-d") . ' ' . date('H:i:s');
            $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
            $centralSale->customer_id = $request->customer_id;
            $centralSale->shipment_id = $request->shipment_id;
            $centralSale->debt = $request->debt;
            $centralSale->total_weight = $request->total_weight;
            $centralSale->total_cost = $request->total_cost;
            $centralSale->discount = $request->discount;
            $centralSale->discount_type = $request->discount_type;
            $centralSale->subtotal = $request->subtotal;
            $centralSale->shipping_cost = $request->shipping_cost;
            $centralSale->other_cost = $request->other_cost;
            $centralSale->detail_other_cost = $request->detail_other_cost;
            $centralSale->deposit_customer = $request->deposit_customer;
            $centralSale->net_total = $request->net_total;
            $centralSale->receipt_1 = $request->receipt_1;
            $centralSale->receive_1 = $request->receive_1;
            $centralSale->receipt_2 = $request->receipt_2;
            $centralSale->receive_2 = $request->receive_2;
            $centralSale->recipient = $request->recipient;
            $centralSale->payment_amount = $request->payment_amount;
            $centralSale->remaining_payment = $request->remaining_payment;
            $centralSale->address_recipient = $request->address_recipient;
            $centralSale->detail = $request->detail;
            $centralSale->status = 'approved';
            $centralSale->save();

            $centralSale->products()->detach();

            $keyedProducts = collect($products)->mapWithKeys(function ($item) {
                return [
                    $item['id'] => [
                        'stock' => $item['central_stock'],
                        'booked' => $item['booked'],
                        'price' => str_replace(".", "", $item['price']),
                        'quantity' => $item['quantity'],
                        'free' => $item['free'],
                        'amount' => $item['subTotal'],
                        'editable' => $item['editable'] == true ? 1 : 0,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ];
            })->all();

            $centralSale->products()->attach($keyedProducts);

            foreach ($oldTakenProducts as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                $productRow->booked = $productRow->booked - $product['taken'];
                // if ($centralSale->status == 'pending') {
                // $productRow->booked = $productRow->booked - $product['taken'];
                // } else if ($centralSale->status == 'approved') {
                //     $productRow->central_stock = $productRow->central_stock - $product['taken'];
                // }
                $productRow->save();
            }


            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);
                // if ($centralSale->status == 'pending') {
                //      $productRow->booked = $productRow->booked + ($product['quantity'] + $product['free']);
                // } else if ($centralSale->status == 'approved') {
                //     $productRow->central_stock = $productRow->central_stock - ($product['quantity'] + $product['free']);
                // }
                // $productRow->ws_price = str_replace(".", "", $product['price']);
                $productRow->save();
            }

            if (($request->receipt_1 !== '' && $request->receipt_1 !== null) && ($request->receive_1 !== '' && $request->receive_1 !== null)) {
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $centralSale->id;
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $amount = $this->clearThousandFormat($request->receive_1);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->receipt_1;
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $amount;
                // $transaction->payment_method = $request->payment_method;
                $transaction->payment_method = 'transfer';
                $transaction->is_init = 1;
                // $transaction->note = $request->note;

                $transaction->save();

                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $amount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                // -------- Save to account transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = $request->receipt_1;
                $accountTransaction->amount = $amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;
                $accountTransaction->save();
                // --------
            }

            if (($request->receipt_2 !== '' && $request->receipt_2 !== null) && ($request->receive_2 !== '' && $request->receive_2 !== null)) {
                $date = $request->date;
                $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
                $saleId = $centralSale->id;
                $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
                $amount = $this->clearThousandFormat($request->receive_2);

                $transaction = new CentralSaleTransaction;
                $transaction->code = $transactionNumber;
                $transaction->date = $date;
                $transaction->account_id = $request->receipt_2;
                $transaction->customer_id = $request->customer_id;
                $transaction->amount = $amount;
                // $transaction->payment_method = $request->payment_method;
                $transaction->payment_method = 'transfer';
                $transaction->is_init = 1;
                // $transaction->note = $request->note;

                $transaction->save();

                $transaction->centralSales()->attach([
                    $saleId => [
                        'amount' => $amount,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]
                ]);

                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = $request->receipt_2;
                $accountTransaction->amount = $amount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Transaksi penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;

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
                $accountTransaction->note = "Diskon penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;
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
                $accountTransaction->note = "Biaya kirim penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;
                $accountTransaction->save();
            }
            // --------

            $receiveAmount1 = $this->clearThousandFormat($request->receive_1);
            $receiveAmount2 = $this->clearThousandFormat($request->receive_2);
            $totalReceiveAmount = (int) $receiveAmount1 + (int) $receiveAmount2;
            if ($totalReceiveAmount < $request->net_total) {

                $accountTransaction = new AccountTransaction;
                $accountTransaction->account_id = config('accounts.piutang', 0);
                $accountTransaction->amount = $request->net_total - $totalReceiveAmount;
                $accountTransaction->type = "in";
                $accountTransaction->note = "Piutang penjualan pusat No. " . $centralSale->code;
                $accountTransaction->date = $request->date;
                $accountTransaction->table_name = 'central_sales';
                $accountTransaction->table_id = $centralSale->id;

                $accountTransaction->save();
            }


            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Internal error detaching products',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    public function reject($id)
    {
        // Save Main Data
        $centralSale = CentralSale::findOrFail($id);

        $products = $centralSale->products;

        DB::beginTransaction();

        try {
            $centralSale->status = 'rejected';
            $centralSale->save();
            foreach ($products as $product) {
                $productRow = Product::find($product->id);
                if ($productRow == null) {
                    continue;
                }
                $finalBooked = $productRow->booked - ($product->pivot->quantity + $product->pivot->free);
                $productRow->booked = $finalBooked < 0 ? 0 : $finalBooked;
                $productRow->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
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

    public function pay($id)
    {
        $sale = CentralSale::with(['customer', 'products'])->findOrFail($id);
        $accounts = Account::where('type', '!=', 'none')->get();


        // return $selectedProducts;
        $transactions = collect($sale->centralSaleTransactions)->sortBy('date')->values()->all();
        $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');

        $sidebarClass = 'compact';

        return view('central-sale.pay', [
            'sale' => $sale,
            'accounts' => $accounts,
            'total_paid' => $totalPaid,
            'transactions' => $transactions,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    public function return($id)
    {

        // return 'asdasd';
        $sale = CentralSale::with(['customer', 'products'])->findOrFail($id);
        $accounts = Account::all();

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

        $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');

        // return $transactions;
        // return $selectedProducts;

        // return $selectedProducts;
        $sidebarClass = 'compact';

        return view('central-sale.return', [
            'sale' => $sale,
            'accounts' => $accounts,
            'total_paid' => $totalPaid,
            'selected_products' => $selectedProducts,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    // public function approve($id)
    // {
    //     $customers = Customer::all();
    //     $shipments = Shipment::all();
    //     $accounts = Account::all();
    //     $centralSales = CentralSale::with(['products'])->findOrFail($id);
    //     $selectedProducts = collect($centralSales->products)->each(function ($product) {
    //         $product['quantity'] = $product->pivot->quantity;
    //         $product['price'] = $product->pivot->price;
    //         $product['stock'] = $product->pivot->stock;
    //         $product['free'] = $product->pivot->free;
    //         // $product['cause'] = 'defective';
    //     });

    //     return view('central-sale.approve', [
    //         'centralSales' => $centralSales,
    //         'customers' => $customers,
    //         'accounts' => $accounts,
    //         'shipments' => $shipments,
    //     ]);
    // } 

    public function print($id)
    {
        // return view('central-sale.print');
        $sale = CentralSale::with(['products', 'customer', 'shipment'])->findOrFail($id);

        $data = [
            'sale' => $sale,
        ];

        // $pdf = PDF::loadView('central-sale.print', $data, [], [
        //     'format' => 'A5',
        // ]);
        // return $pdf->stream($sale->code . '.pdf');
        $mpdf = new \Mpdf\Mpdf([
            //     'mode' => 'utf-8', 'format' =>
            //     'A4', 'defaultPageNumStyle' => '1',
            'margin_right' => '4',
            'margin_left' => '4',
            'margin_bottom' => '4',
            'margin_top' => '4',
            'format' => 'A5',
        ]);
        $pdf = view('central-sale.print', $data);

        //   $mpdf->setFooter('{PAGENO}');
        $mpdf->WriteHTML($pdf);
        $mpdf->Output();
    }

    public function updatePrintStatus(Request $request, $id)
    {
        $sale = CentralSale::findOrFail($id);

        try {
            $sale->is_printed = 1;
            $sale->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $sale,
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

    public function authProductPrice(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        $user = User::with('group')->where('username', $username)->first();
        if ($user == null) {
            return response()->json([
                'message' => 'Username atau password salah',
                'code' => 400,
                'error' => true,
            ], 400);
        }

        if (!Hash::check($password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah',
                'code' => 400,
                'error' => true,
            ], 400);
        }

        if ($user->group == null) {
            return response()->json([
                'message' => 'User tidak memiliki grup',
                'code' => 400,
                'error' => true,
            ], 400);
        }

        $permissions = json_decode($user->group->permission);
        if (in_array('edit_product', $permissions)) {
            return response()->json([
                'message' => 'OK',
                'code' => 200,
                'error' => false,
                'data' => [
                    'user' => $user,
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'User tidak memiliki akses untuk mengubah harga',
                'code' => 500,
                'error' => true,
            ], 500);
        }

        return response()->json([
            'message' => 'Internal error',
            'code' => 500,
            'error' => true,
        ], 500);
    }

    public function reportByCustomer(Request $request)
    {

        // return CentralSale::with(['products', 'customer'])->get()->groupBy(function ($item, $key) {
        //     return $item->customer->name;
        // })->map(function ($item, $customer) {
        //     $totalCustomer = collect($item)->sum('total_cost');
        //     return [
        //         'customer' => $customer,
        //         'total' => $totalCustomer
        //     ];
        // })->values()->all();

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new CentralSaleByCustomerDetailExport($request->all()), 'Central Sales By Customer Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
            return Excel::download(new CentralSaleByCustomerSummaryExport($request->all()), 'Central Sales By Customer Summary ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
    }

    public function reportByProduct(Request $request)
    {

        // return Product::with(['productCategory', 'productSubcategory', 'centralSales'])->get()
        //     ->map(function ($product, $key) {
        //         $totalQuantity = collect($product->centralSales)->sum(function ($item) {
        //             return $item->pivot->quantity;
        //         });
        //         $totalAmount = collect($product->centralSales)->sum('total_cost');
        //         $avaregePrice = collect($product->centralSales)->average(function ($item) {
        //             return $item->pivot->price;
        //         });
        //         return [
        //             'category' => $product->productCategory->name,
        //             'subcategory' => $product->productSubcategory->name,
        //             'name' => $product->name,
        //             'quantity' => $totalQuantity,
        //             'amount' => $totalAmount,
        //             'avg_price' => $avaregePrice,
        //         ];
        //     })
        //     ->groupBy('category')
        //     // ->groupBy(function ($item) {
        //     //     return $item->productCategory->name;
        //     // })
        //     // ->map(function ($item, $key) {
        //     //     $
        //     //     return [
        //     //         'key' => $key,
        //     //     ];
        //     // })

        //     // ->mapWithKeys(function ($item, $category) {
        //     //     $subcategoryGroup = collect($item)->values()->groupBy(function ($product) {
        //     //         return $product->productSubcategory->name;
        //     //     })->all();
        //     //     return [
        //     //         $category => $subcategoryGroup,
        //     //     ];
        //     // })
        //     ->all();

        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $reportType = $request->query('report_type');

        if ($reportType == 'detail') {
            return Excel::download(new CentralSaleByProductDetailExport($request->all()), 'Central Sales By Product Detail ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else if ($reportType == 'summary') {
            return Excel::download(new CentralSaleByProductSummaryExport($request->all()), 'Central Sales By Product Summary ' . $startDate . ' - ' . $endDate . '.xlsx');
        } else {
            return response()->json([
                'msg' => 'Unknown report type'
            ], 400);
        }

        return;
    }

    public function datatableProducts()
    {
        $products = Product::with('productCategory')->with('productSubcategory')->get();
        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('action', function () {
                $button = '<button class="btn btn-outline-primary btn-sm btn-choose"><em class="fas fa-plus"></em>&nbsp;Pilih</button>';
                return $button;
            })
            ->make(true);
    }

    public function datatableCentralSale()
    {
        $centralSale = CentralSale::with(['products', 'shipment', 'createdBy', 'centralSaleTransactions'])->select('central_sales.*');
        return DataTables::of($centralSale)
            ->addIndexColumn()
            ->addColumn('shipment_name', function ($row) {
                return ($row->shipment ? $row->shipment->name : "");
            })
            ->addColumn('sales_name', function ($row) {
                return ($row->createdBy ? $row->createdBy->name : "");
            })
            ->addColumn('status', function ($row) {
                // $button = $row->status;
                // if ($button == 'pending') {
                //     return "<a href='/central-sale/approval/{$row->id}' class='btn btn-warning'>
                //     <span>Pending</span>
                //     </a>";
                // }
                // if ($button == 'approved') {
                //     return "Approved";
                // } else {
                //     return "Rejected";
                // }
                $color = 'primary';
                switch ($row->status) {
                    case 'pending':
                        $color = 'warning';
                        break;
                    case 'approved':
                        $color = 'success';
                        break;
                    case 'rejected':
                        $color = 'danger';
                        break;
                    default:
                        $color = 'primary';
                };
                return '<span class="badge badge-' . $color . ' text-capitalize">' . $row->status . '</span>';
            })
            ->addColumn('print_status', function ($row) {
                if ($row->is_printed == 0) {
                    return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
                } else {
                    return '<em class="icon ni ni-check-circle-fill text-success" style="font-size: 1.5em"></em>';
                }

                return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
            })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/central-sale/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/central-sale/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';


                if ($row->status == 'pending') {
                    $button .= '<a href="/central-sale/approval/' . $row->id . '"><em class="icon fas fa-check"></em>
                <span>Approval</span>
            </a>';
                }

                if ($row->status == 'approved') {
                    $button .= '<a href="/central-sale/return/' . $row->id . '"><em class="icon fas fa-undo"></em><span>Retur</span></a>';
                    $button .= '<a href="/central-sale/pay/' . $row->id . '"><em class="icon fas fa-credit-card"></em><span>Bayar</span></a>';
                }

                $button .= '<a href="/central-sale/print/' . $row->id . '" class="btn-print" data-print="' . $row->is_printed . '"  data-id="' . $row->id . '"><em class="icon fas fa-print"></em>
                <span>Cetak</span>
            </a>';

                $button .= '           
                    </ul>
                </div>
            </div>';
                return $button;
            })
            ->rawColumns(['status', 'print_status', 'action'])
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
