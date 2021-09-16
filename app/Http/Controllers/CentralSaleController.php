<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CentralSale;
use App\Models\CentralSaleReturn;
use App\Models\CentralSaleTransaction;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Shipment;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

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
        $accounts = Account::all();
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
    public function store(Request $request)
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
                    $product['subTotal'] = $product['agent_price'];
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $centralSales = CentralSale::with('products')->findOrFail($id);
        return view('central-sale.show', [
            'centralSales' => $centralSales,
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
        $customers = Customer::all();
        $shipments = Shipment::all();
        $accounts = Account::all();
        $centralSales = CentralSale::with(['products'])->findOrFail($id);
        $selectedProducts = collect($centralSales->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
            $product['price'] = $product->pivot->price;
            $product['stock'] = $product->pivot->stock;
            $product['free'] = $product->pivot->free;
            // $product['cause'] = 'defective';
        });

        return view('central-sale.edit', [
            'centralSales' => $centralSales,
            'customers' => $customers,
            'accounts' => $accounts,
            'shipments' => $shipments,
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
        $centralSale = CentralSale::findOrFail($id);
        $centralSale->code = $request->code;
        $centralSale->date = $request->date;
        $centralSale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
        $centralSale->customer_id = $request->customer_id;
        $centralSale->shipment_id = $request->shipment_id;
        $centralSale->debt = $request->debt;
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
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

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'price' => str_replace(".", "", $item['agent_price']),
                    'quantity' => $item['quantity'],
                    'free' => $item['free'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $centralSale->products()->detach();
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
            $centralSale->products()->attach($keyedProducts);
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

                // $productRow->central_stock = $productRow->central_stock - $product['quantity'];
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CentralSale::destroy($id);
        return redirect('/central-sale');
        // $centralsale = CentralSale::findOrFail($id);
        // try {
        //     $centralsale->delete();
        //     return response()->json([
        //         'message' => 'Data has been saved',
        //         'code' => 200,
        //         'error' => false,
        //         'data' => $centralsale,
        //     ]);
        // } catch (Exception $e) {
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }
    }

    public function approval($id)
    {
        $centralSale = CentralSale::with(['products'])->findOrFail($id);

        if ($centralSale->status !== 'pending') {
            return redirect('/central-sale');
        }

        $customers = Customer::all();
        $shipments = Shipment::all();
        $accounts = Account::all();
        $selectedProducts = collect($centralSale->products)->each(function ($product) {
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

        return view('central-sale.approval', [
            'central_sale' => $centralSale,
            'customers' => $customers,
            'accounts' => $accounts,
            'shipments' => $shipments,
            'selected_products' => $selectedProducts,
        ]);
    }

    public function approve(Request $request, $id)
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
                    $product['subTotal'] = $product['agent_price'];
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
            $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
            $amount = $this->clearThousandFormat($request->receive_1);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $date;
            $transaction->account_id = $request->receipt_1;
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            // $transaction->payment_method = $request->payment_method;
            $transaction->payment_method = 'transfer';
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
        }

        if (($request->receipt_2 !== '' && $request->receipt_2 !== null) && ($request->receive_2 !== '' && $request->receive_2 !== null)) {
            $date = $request->date;
            $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
            $saleId = $centralSale->id;
            $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);
            $amount = $this->clearThousandFormat($request->receive_2);

            $transaction = new CentralSaleTransaction;
            $transaction->code = $transactionNumber;
            $transaction->date = $date;
            $transaction->account_id = $request->receipt_2;
            $transaction->customer_id = $request->customer_id;
            $transaction->amount = $amount;
            // $transaction->payment_method = $request->payment_method;
            $transaction->payment_method = 'transfer';
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
        }

        return response()->json([
            'message' => 'Data has been saved',
            'code' => 200,
            'error' => false,
            'data' => $centralSale,
        ]);
    }

    public function reject($id)
    {
        // Save Main Data
        $centralSale = CentralSale::findOrFail($id);

        $products = $centralSale->products;

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $products,
        // ]);

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product->id);
                if ($productRow == null) {
                    continue;
                }
                $finalBooked = $productRow->booked - ($product->pivot->quantity + $product->pivot->free);
                $productRow->booked = $finalBooked < 0 ? 0 : $finalBooked;

                $productRow->save();
            }
        } catch (Exception $e) {
            // $centralSale->products()->detach();
            // $centralSale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        try {
            $centralSale->status = 'rejected';

            $centralSale->save();

            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $centralSale,
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

    public function pay($id)
    {
        $sale = CentralSale::with(['customer', 'products'])->findOrFail($id);
        $accounts = Account::all();

        // return $purchase;

        // foreach($purchase->products)
        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['quantity'] = $product->pivot->quantity;
        //     $product['purchase_price'] = $product->pivot->price;
        // });

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

    public function approved(Request $request, $id)
    {
        $centralSale = CentralSale::findOrFail($id);
        $centralSale->code = $request->code;
        $centralSale->date = $request->date;
        $centralSale->customer_id = $request->customer_id;
        $centralSale->shipment_id = $request->shipment_id;
        $centralSale->debt = $request->debt;
        $centralSale->total_weight = $request->total_weight;
        $centralSale->total_cost = $request->total_cost;
        $centralSale->discount = $request->discount;
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
        $centralSale->status = "approved";
        $products = $request->selected_products;

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

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'stock' => $item['central_stock'],
                    'price' => str_replace(".", "", $item['agent_price']),
                    'quantity' => $item['quantity'],
                    'free' => $item['free'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $centralSale->products()->detach();
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
            $centralSale->products()->attach($keyedProducts);
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

                $productRow->central_stock = $productRow->central_stock - $product['quantity'];
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
        $centralSale = CentralSale::with('products')->with('shipment')->orderBy('date', 'desc')->select('central_sales.*');
        return DataTables::of($centralSale)
            ->addIndexColumn()
            ->addColumn('shipment_name', function ($row) {
                return ($row->shipment ? $row->shipment->name : "");
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

                $button .= '           
                    </ul>
                </div>
            </div>';
                return $button;
            })
            ->rawColumns(['status', 'action'])
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
