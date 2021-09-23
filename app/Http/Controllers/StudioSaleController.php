<?php

namespace App\Http\Controllers;

use App\Exports\StudioSaleDetailExport;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Product;
use App\Models\StudioSale;
use App\Models\StudioSaleReturn;
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
        $accounts = Account::all();
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

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $request->all(),
        // ]);

        $date = $request->date;

        $salesByCurrentDateCount = StudioSale::query()->where('date', $date)->get()->count();
        $saleNumber = 'RS/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $salesByCurrentDateCount + 1);

        $sale = new StudioSale;
        $sale->code = $saleNumber;
        $sale->date = $request->date . ' ' . date('H:i:s');
        // $sale->due_date = date('Y-m-d', strtotime("+" . $request->debt . " day", strtotime($request->date)));
        // $sale->customer_id = $request->customer_id;
        // $sale->shipment_id = $request->shipment_id;
        // $sale->debt = $request->debt;
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
        // $sale->remaining_payment = $request->remaining_payment;
        // $sale->address_recipient = $request->address_recipient;
        // $sale->detail = $request->detail;
        $sale->payment_method = $request->payment_method;
        $sale->account_id = $request->account_id;
        $sale->created_by = Auth::id();
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

        try {
            $sale->save();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $sale,
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

        try {
            $sale->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $sale,
            // ]);
        } catch (Exception $e) {
            $sale->delete();
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
                $productRow->studio_stock -= ($product['quantity'] + $product['free']);
                $productRow->save();
            }
        } catch (Exception $e) {
            $sale->products()->detach();
            $sale->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Account Transaction
        // $saleReturn = CentralSaleReturn::find($saleReturnId);
        $accountTransaction = new AccountTransaction;
        $accountTransaction->account_in = $request->account_id;
        $accountTransaction->amount = $this->clearThousandFormat($request->net_total);
        $accountTransaction->type = "in";
        $accountTransaction->note = "Penjualan studio No. " . $saleNumber;
        $accountTransaction->date = $request->date;

        try {
            $accountTransaction->save();
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

        return response()->json([
            'message' => 'Data has been saved',
            'code' => 200,
            'error' => false,
            'data' => $sale,
        ]);
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
        $sale = StudioSale::findOrFail($id);
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

        try {
            $sale->delete();
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

    public function return($id)
    {

        // return 'asdasd';
        $sale = StudioSale::with(['products'])->findOrFail($id);
        $accounts = Account::all();

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

        $pdf = PDF::loadView('studio-sale.print', $data);
        return $pdf->stream($sale->code . '.pdf');
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
        $studioSales = StudioSale::with('products')->orderBy('date', 'desc')->select('studio_sales.*');
        return DataTables::of($studioSales)
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

    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }

    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }
}
