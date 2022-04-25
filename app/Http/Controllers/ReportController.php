<?php

namespace App\Http\Controllers;

use App\Exports\CentralPurchaseHutangByCustomerSummayExport;
use App\Exports\CentralPurchaseHutangBySupplierDetailExport;
use App\Models\CentralPurchase;
use App\Models\CentralSale;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;
use App\Models\RetailSale;
use App\Models\Shipment;
use App\Models\StudioSale;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.index');
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
        //
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

    public function centralSaleByCustomerDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.central-sale-by-customer', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function centralSaleByCustomerDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $query = CentralSale::with(['products', 'customer'])->whereBetween('date', [$startDate, $endDate]);
        $query = CentralSale::with(['createdBy', 'products' => function ($q) {
            $q->with(['productCategory', 'productSubcategory']);
        }, 'customer'])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        if ($customer !== '' && $customer !== null) {
            $query->where('customer_id', $customer);
        }

        if ($shipment !== '' && $shipment !== null) {
            $query->where('shipment_id', $shipment);
        }

        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $sales_by_customer = $query->get()->groupBy(function ($item, $key) {
            return $item->customer->name;
        })->all();

        $emptyCellCount = 8;
        $totalColumn = 9;
        $html = '';

        if (count($sales_by_customer) > 0) {
            foreach ($sales_by_customer as $customer => $sales) {
                $html .= '<tr>';
                $html .= '<td>' . $customer . '</td>';
                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }
                $html .= '</tr>';

                $totalByCustomer = 0;

                foreach ($sales as $sale) {
                    foreach ($sale->products as $product) {
                        $html .= '<tr>';
                        $html .= '<td></td>';
                        $html .= '<td>' . $sale->date . '</td>';
                        $html .= '<td>' . $sale->code . '</td>';
                        if ($sale->createdBy !== null) {
                            $html .= '<td>' . $sale->createdBy->name . '</td>';
                        } else {
                            $html .= '<td></td>';
                        }

                        $html .= '<td>';
                        if ($product->productCategory !== null) {
                            $html .= $product->productCategory->name . ':';
                        }
                        if ($product->productSubcategory !== null) {
                            $html .= $product->productSubcategory->name . ' - ';
                        }
                        $html .= $product->name;
                        $html .= '</td>';
                        $html .= '<td>' . number_format($product->pivot->quantity) . '</td>';
                        $html .= '<td>' . number_format($product->pivot->free) . '</td>';
                        $html .= '<td class="text-right">' . number_format($product->pivot->price) . '</td>';
                        $html .= '<td class="text-right">' . number_format($product->pivot->amount) . '</td>';
                        $html .= '</tr>';
                        $totalByCustomer += $product->pivot->amount;
                    }
                }

                $html .= '<tr class="border-top">';
                $html .= '<td>Total For ' . $customer . '</td>';
                for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($totalByCustomer) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        // $html = '</tbody>';

        // return response()->json([
        //     'data' => $sales,
        // ]);
        return $html;
    }

    public function centralSaleByCustomerSummary()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.summary.central-sale-by-customer', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function centralSaleByCustomerSummaryData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $query = CentralSale::with(['products', 'customer'])->whereBetween('date', [$startDate, $endDate]);
        $query = CentralSale::with(['customer'])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        if ($customer !== '' && $customer !== null) {
            $query->where('customer_id', $customer);
        }

        if ($shipment !== '' && $shipment !== null) {
            $query->where('shipment_id', $shipment);
        }

        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $sales = $query->get()
            ->groupBy(function ($item, $key) {
                return $item->customer->name;
            })
            ->map(function ($item, $customer) {
                $totalCustomer = collect($item)->sum('total_cost');
                return [
                    'customer' => $customer,
                    'total' => $totalCustomer
                ];
            })->values()->all();

        $html = '';

        $totalColumn = 2;

        $total = 0;
        if (count($sales) > 0) {
            foreach ($sales as $sale) {
                $html .= '<tr>';
                $html .= '<td>' . $sale['customer'] . '</td>';
                $html .= '<td class="text-right">' . number_format($sale['total']) . '</td>';
                $html .= '</tr>';
                $total += $sale['total'];
            }

            $html .=  '<tr>';
            $html .= '<td>TOTAL</td>';
            $html .= '<td class="text-right">' . number_format($total) . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    // public function centralSaleByCustomerDetailData(Request $request)
    // {
    //     // $columnSelections = explode(',', $request->query('columns'));
    //     $startDate = $request->query('start_date');
    //     $endDate = $request->query('end_date');
    //     $status = $request->query('status');
    //     $customer = $request->query('customer');
    //     $sortBy = $request->query('sort_by');
    //     $sortIn = $request->query('sort_in');
    //     // $estimations = Estimation::with(['customer'])->select('estimations.*');
    //     $query = CentralSale::with(['customer', 'shipment'])->select('central_sales.*')->whereBetween('date', [$startDate, $endDate]);

    //     if ($status !== '' && $status !== null) {
    //         $query->where('status', $status);
    //     }

    //     if ($customer !== '' && $customer !== null) {
    //         $query->where('customer_id', $customer);
    //     }

    //     if ($sortBy !== '' && $sortBy !== null) {
    //         $query->orderBy($sortBy, $sortIn);
    //     }

    //     $estimations = $query->get();

    //     return DataTables::of($estimations)
    //         ->addIndexColumn()
    //         ->addColumn('shipment_name', function ($row) {
    //             return ($row->shipment ? $row->shipment->name : "");
    //         })
    //         ->addColumn('status', function ($row) {
    //             // $button = $row->status;
    //             // if ($button == 'pending') {
    //             //     return "<a href='/central-sale/approval/{$row->id}' class='btn btn-warning'>
    //             //     <span>Pending</span>
    //             //     </a>";
    //             // }
    //             // if ($button == 'approved') {
    //             //     return "Approved";
    //             // } else {
    //             //     return "Rejected";
    //             // }
    //             $color = 'primary';
    //             switch ($row->status) {
    //                 case 'pending':
    //                     $color = 'warning';
    //                     break;
    //                 case 'approved':
    //                     $color = 'success';
    //                     break;
    //                 case 'rejected':
    //                     $color = 'danger';
    //                     break;
    //                 default:
    //                     $color = 'primary';
    //             };
    //             return '<span class="badge badge-' . $color . ' text-capitalize">' . $row->status . '</span>';
    //         })
    //         ->addColumn('print_status', function ($row) {
    //             if ($row->is_printed == 0) {
    //                 return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
    //             } else {
    //                 return '<em class="icon ni ni-check-circle-fill text-success" style="font-size: 1.5em"></em>';
    //             }

    //             return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
    //         })
    //         ->rawColumns(['status', 'print_status'])
    //         ->make(true);
    // }

    public function centralSaleByProductDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.central-sale-by-product', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function centralSaleByProductDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');

        $query = Product::whereHas('centralSales', function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        })->with(['productCategory', 'productSubcategory', 'centralSales' => function ($query) use ($customer, $shipment, $status) {
            $query->with(['customer', 'createdBy']);
            if ($customer !== '' && $customer !== null) {
                $query->where('customer_id', $customer);
            }

            if ($shipment !== '' && $shipment !== null) {
                $query->where('shipment_id', $shipment);
            }

            if ($status !== '' && $status !== null) {
                $query->where('status', $status);
            }
        }]);

        $sales_by_product = $query->get();

        $grandTotal = 0;
        $emptyCellCount = 8;
        $totalColumn = 9;

        $html = '';

        if (count($sales_by_product) > 0) {
            foreach ($sales_by_product as $product) {

                $html .= '<tr>';
                $html .= '<td>' . $product->name . '</td>';
                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }
                $html .= '</tr>';
                $totalByProduct = 0;

                foreach ($product->centralSales as $sale) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td>' . $sale->date . '</td>';
                    $html .= '<td>' . $sale->code . '</td>';
                    if ($sale->createdBy !== null) {
                        $html .= '<td>' . $sale->createdBy->name . '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    if ($sale->customer !== null) {
                        $html .= '<td>' . $sale->customer->name . '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '<td>' . number_format($sale->pivot->quantity) . '</td>';
                    $html .= '<td>' . number_format($sale->pivot->free) . '</td>';
                    $html .= '<td class="text-right">' . number_format($sale->pivot->price) . '</td>';
                    $html .= '<td class="text-right">' . number_format($sale->pivot->amount) . '</td>';
                    $html .= '</tr>';
                    $totalByProduct += $sale->pivot->amount;
                }

                $html .= '<tr>';
                $html .= '<td>Total For ' . $product->name . '</td>';
                for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($totalByProduct) . '</td>';
                $html .= '</tr>';
                $grandTotal += $totalByProduct;
            }

            $html .= '<tr>';
            $html .= '<td>TOTAL</td>';
            for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                $html .= '<td></td>';
            }
            $html .= '<td class="text-right">' . number_format($grandTotal) . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function centralSaleByProductSummary()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.summary.central-sale-by-product', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function centralSaleByProductSummaryData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');

        $query = Product::whereHas('centralSales', function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        })->with(['productCategory', 'productSubcategory', 'centralSales' => function ($query) use ($customer, $shipment, $status) {
            $query->with(['customer']);
            if ($customer !== '' && $customer !== null) {
                $query->where('customer_id', $customer);
            }

            if ($shipment !== '' && $shipment !== null) {
                $query->where('shipment_id', $shipment);
            }

            if ($status !== '' && $status !== null) {
                $query->where('status', $status);
            }
        }]);

        $sales = $query->get()
            ->map(function ($product, $key) {
                $totalQuantity = collect($product->centralSales)->sum(function ($item) {
                    return $item->pivot->quantity;
                });
                $totalAmount = collect($product->centralSales)->sum('total_cost');
                $avaregePrice = collect($product->centralSales)->average(function ($item) {
                    return $item->pivot->price;
                });
                return [
                    'category' => $product->productCategory->name,
                    'subcategory' => $product->productSubcategory->name,
                    'name' => $product->name,
                    'quantity' => $totalQuantity,
                    'amount' => $totalAmount,
                    'avg_price' => $avaregePrice,
                ];
            })
            ->groupBy('category')

            ->all();

        $html = '';

        $totalColumn = 5;

        if (count($sales) > 0) {
            foreach ($sales as $category => $products) {
                $html .= '<tr>';
                $html .= '<td>' . $category . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '</tr>';
                $totalAmountByCategory = 0;

                foreach ($products as $product) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td>' . $product['name'] . '</td>';
                    $html .= '<td>' . number_format($product['quantity']) . '</td>';
                    $html .= '<td class="text-right">' . number_format($product['amount']) . '</td>';
                    $html .= '<td class="text-right">' . number_format($product['avg_price']) . '</td>';
                    $html .= '</tr>';
                    $totalAmountByCategory += $product['amount'];
                }

                $html .= '<tr>';
                $html .= '<td>Total For ' . $category . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td class="text-right">' . number_format($totalAmountByCategory) . '</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function retailSaleDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.retail-sale', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function retailSaleDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $query = CentralSale::with(['products', 'customer'])->whereBetween('date', [$startDate, $endDate]);
        $query = RetailSale::with(['createdBy', 'products' => function ($q) {
            $q->with(['productCategory', 'productSubcategory']);
        }])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        // if ($customer !== '' && $customer !== null) {
        //     $query->where('customer_id', $customer);
        // }

        // if ($shipment !== '' && $shipment !== null) {
        //     $query->where('shipment_id', $shipment);
        // }

        // if ($status !== '' && $status !== null) {
        //     $query->where('status', $status);
        // }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $sales = $query->get();

        $html = '';

        $emptyCellCount = 5;
        $totalColumn = 8;
        $total = 0;

        if (count($sales) > 0) {
            foreach ($sales as $sale) {
                $invoiceTotal = 0;
                $html .= '<tr>';
                $html .= '<td>' . $sale->code . '</td>';
                $html .= '<td>' . $sale->date . '</td>';
                if ($sale->createdBy !== null) {
                    $html .= '<td>' . $sale->createdBy->name . '</td>';
                } else {
                    $html .= '<td>-</td>';
                }

                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }

                $html .= '</tr>';

                foreach ($sale->products as $product) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';

                    $html .= '<td>';
                    if ($product->productCategory !== null) {
                        $html .= $product->productCategory->name . ':';
                    }
                    if ($product->productSubcategory !== null) {
                        $html .= $product->productSubcategory->name . ' - ';
                    }
                    $html .= $product->name;
                    $html .= '</td>';

                    $html .= '<td>' . number_format($product->pivot->quantity) . '</td>';
                    $html .=  '<td>' . number_format($product->pivot->free)  . '</td>';
                    $html .=  '<td class="text-right">' . number_format($product->pivot->price)  . '</td>';
                    $amount = $product->pivot->quantity * $product->pivot->price;
                    $html .=  '<td class="text-right">' . number_format($amount)  . '</td>';
                    $html .= '</tr>';
                    $total += $amount;
                    $invoiceTotal += $amount;
                }

                $html .= '<tr>';
                $html .= '<td>Total for' . $sale->code . '</td>';
                for ($i = 0; $i < ($emptyCellCount + 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($invoiceTotal) . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>';
            $html .= '<td>Total</td>';
            for ($i = 0; $i < ($emptyCellCount + 1); $i++) {
                $html .= '<td></td>';
            }
            $html .= '<td class="text-right">' . number_format($total) . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function studioSaleDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.studio-sale', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function studioSaleDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $customer = $request->query('customer');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $query = CentralSale::with(['products', 'customer'])->whereBetween('date', [$startDate, $endDate]);
        $query = StudioSale::with(['createdBy', 'products' => function ($q) {
            $q->with(['productCategory', 'productSubcategory']);
        }])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        // if ($customer !== '' && $customer !== null) {
        //     $query->where('customer_id', $customer);
        // }

        // if ($shipment !== '' && $shipment !== null) {
        //     $query->where('shipment_id', $shipment);
        // }

        // if ($status !== '' && $status !== null) {
        //     $query->where('status', $status);
        // }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $sales = $query->get();

        $html = '';

        $emptyCellCount = 5;
        $totalColumn = 8;
        $total = 0;

        if (count($sales) > 0) {
            foreach ($sales as $sale) {
                $invoiceTotal = 0;
                $html .= '<tr>';
                $html .= '<td>' . $sale->code . '</td>';
                $html .= '<td>' . $sale->date . '</td>';
                if ($sale->createdBy !== null) {
                    $html .= '<td>' . $sale->createdBy->name . '</td>';
                } else {
                    $html .= '<td>-</td>';
                }

                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }

                $html .= '</tr>';

                foreach ($sale->products as $product) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';
                    $html .= '<td></td>';

                    $html .= '<td>';
                    if ($product->productCategory !== null) {
                        $html .= $product->productCategory->name . ':';
                    }
                    if ($product->productSubcategory !== null) {
                        $html .= $product->productSubcategory->name . ' - ';
                    }
                    $html .= $product->name;
                    $html .= '</td>';

                    $html .= '<td>' . number_format($product->pivot->quantity) . '</td>';
                    $html .=  '<td>' . number_format($product->pivot->free)  . '</td>';
                    $html .=  '<td class="text-right">' . number_format($product->pivot->price)  . '</td>';
                    $amount = $product->pivot->quantity * $product->pivot->price;
                    $html .=  '<td class="text-right">' . number_format($amount)  . '</td>';
                    $html .= '</tr>';
                    $total += $amount;
                    $invoiceTotal += $amount;
                }

                $html .= '<tr>';
                $html .= '<td>Total for' . $sale->code . '</td>';
                for ($i = 0; $i < ($emptyCellCount + 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($invoiceTotal) . '</td>';
                $html .= '</tr>';
            }

            $html .= '<tr>';
            $html .= '<td>Total</td>';
            for ($i = 0; $i < ($emptyCellCount + 1); $i++) {
                $html .= '<td></td>';
            }
            $html .= '<td class="text-right">' . number_format($total) . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function centralPurchaseBySupplierDetail()
    {
        $suppliers = Supplier::all();
        $shipments = Shipment::all();
        return view('report.purchases.detail.central-purchase-by-supplier', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
        ]);
    }

    public function centralPurchaseBySupplierDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplier = $request->query('supplier');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $query = CentralSale::with(['products', 'supplier'])->whereBetween('date', [$startDate, $endDate]);
        $query = CentralPurchase::with(['createdBy', 'products' => function ($q) {
            $q->with(['productCategory', 'productSubcategory']);
        }, 'supplier'])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        if ($supplier !== '' && $supplier !== null) {
            $query->where('supplier_id', $supplier);
        }

        // if ($shipment !== '' && $shipment !== null) {
        //     $query->where('shipment_id', $shipment);
        // }

        // if ($status !== '' && $status !== null) {
        //     $query->where('status', $status);
        // }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $purchases_by_supplier = $query->get()->groupBy(function ($item, $key) {
            return $item->supplier->name;
        })->all();

        $html = '';

        $emptyCellCount = 8;
        $totalColumn = 9;

        if (count($purchases_by_supplier) > 0) {
            foreach ($purchases_by_supplier as $supplier => $purchases) {
                $html .= '<tr>';
                $html .= '<td>' . $supplier . '</td>';
                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }
                $html .= '</tr>';
                $totalBySupplier = 0;

                foreach ($purchases as $purchase) {
                    foreach ($purchase->products as $product) {
                        $html .= '<tr>';
                        $html .= '<td></td>';
                        $html .= '<td>' . $purchase->date . '</td>';
                        $html .= '<td>' . $purchase->code . '</td>';
                        if ($purchase->createdBy !== null) {
                            $html .= '<td>' . $purchase->createdBy->name . '</td>';
                        } else {
                            $html .= '<td></td>';
                        }
                        $html .=  '<td>';
                        if ($product->productCategory !== null) {
                            $html .=  $product->productCategory->name . ':';
                        }

                        if ($product->productSubcategory !== null) {
                            $html .= $product->productSubcategory->name . ' - ';
                        }

                        $html .= $product->name;
                        $html .= '</td>';
                        $html .= '<td>' . number_format($product->pivot->quantity) . '</td>';
                        $html .= '<td>' . number_format($product->pivot->free) . '</td>';
                        $html .= '<td class="text-right">' . number_format($product->pivot->price) . '</td>';
                        $amount = $product->pivot->quantity * $product->pivot->price;
                        $html .= '<td class="text-right">' . number_format($amount) . '</td>';
                        $html .= '</tr>';
                        $totalBySupplier += $amount;
                    }
                }

                $html .= '<tr>';
                $html .= '<td>Total For' . $supplier . '</td>';
                for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($totalBySupplier) . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function centralPurchaseBySupplierSummary()
    {
        $suppliers = Supplier::all();
        $shipments = Shipment::all();
        return view('report.purchases.summary.central-purchase-by-supplier', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
        ]);
    }

    public function centralPurchaseBySupplierSummaryData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplier = $request->query('supplier');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');

        $query = Supplier::whereHas('centralPurchases', function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        })->with(['centralPurchases' => function ($query) use ($supplier) {
            $query->with(['supplier', 'products']);

            if ($supplier !== '' && $supplier !== null) {
                $query->where('supplier_id', $supplier);
            }
        }]);

        $purchases = $query->get()->map(function ($supplier, $key) {
            $totalForSupplier = collect($supplier->centralPurchases)->sum(function ($purchase) {
                return collect($purchase->products)->sum(function ($product) {
                    return $product->pivot->quantity * $product->pivot->price;
                });
            });
            return [
                'supplier' => $supplier->name,
                'total' => $totalForSupplier,
            ];
        })->all();

        $html = '';

        $total = 0;
        $totalColumn = 2;

        if (count($purchases) > 0) {
            foreach ($purchases as $purchase) {
                $html .= '<tr>';
                $html .= '<td>' . $purchase['supplier'] . '</td>';
                $html .= '<td class="text-right">' . number_format($purchase['total']) . '</td>';
                $html .= '</tr>';
                $total += $purchase['total'];
            }

            $html .= '<tr>';
            $html .= '<td>TOTAL</td>';
            $html .= '<td class="text-right">' . number_format($total) . '</td>';
            $html .= '</tr>';
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function centralPurchaseByProductDetail()
    {
        $suppliers = Supplier::all();
        $shipments = Shipment::all();
        return view('report.purchases.detail.central-purchase-by-product', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
        ]);
    }

    public function centralPurchaseByProductDetailData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplier = $request->query('supplier');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');

        $query = Product::whereHas('centralPurchases', function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        })->with(['productCategory', 'productSubcategory', 'centralPurchases' => function ($query) use ($supplier, $shipment, $status) {
            $query->with(['supplier', 'createdBy']);
            if ($supplier !== '' && $supplier !== null) {
                $query->where('supplier_id', $supplier);
            }
        }]);

        $purchases_by_product = $query->get();

        $grandTotal = 0;
        $emptyCellCount = 8;
        $totalColumn = 9;

        $html = '';

        if (count($purchases_by_product) > 0) {
            foreach ($purchases_by_product as $product) {
                $html .=  '<tr>';
                $html .= '<td>' . $product->name . '</td>';
                for ($i = 0; $i < $emptyCellCount; $i++) {
                    $html .= '<td></td>';
                }
                $html .= '</tr>';
                $totalByProduct = 0;

                foreach ($product->centralPurchases as $purchase) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td>' . $purchase->date . '</td>';
                    $html .= '<td>' . $purchase->code . '</td>';
                    if ($product->createdBy !== null) {
                        $html .= '<td>' . $product->createdBy->name . '</td>';
                    } else {
                        $html .= '<td></td>';
                    }
                    $html .= '<td>' . $purchase->supplier->name . '</td>';
                    $html .= '<td>' . number_format($purchase->pivot->quantity) . '</td>';
                    $html .= '<td>' . number_format($purchase->pivot->free) . '</td>';
                    $html .= '<td class="text-right">' . number_format($purchase->pivot->price) . '</td>';
                    $amount = $purchase->pivot->quantity * $purchase->pivot->price;
                    $html .= '<td class="text-right">' . number_format($amount) . '</td>';

                    $html .= '</tr>';
                    $totalByProduct += $amount;
                }

                $html .= '<tr>';
                $html .= '<td>Total For ' . $product->name . '</td>';
                for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                    $html .= '<td></td>';
                }
                $html .= '<td class="text-right">' . number_format($totalByProduct) . '</td>';
                $html .= '</tr>';
                $grandTotal += $totalByProduct;
            }

            $html .= '<tr>';
            $html .= '<td>TOTAL</td>';
            for ($i = 0; $i < ($emptyCellCount - 1); $i++) {
                $html .= '<td></td>';
            }
            $html .= '<td class="text-right">' . number_format($grandTotal) . '</td>';
            $html .= '</tr>';

            // return $html;
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }

    public function centralPurchaseByProductSummary()
    {
        $suppliers = Supplier::all();
        $shipments = Shipment::all();
        return view('report.purchases.summary.central-purchase-by-product', [
            'suppliers' => $suppliers,
            'shipments' => $shipments,
        ]);
    }

    public function centralPurchaseByProductSummaryData(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $supplier = $request->query('supplier');
        $shipment = $request->query('shipment');
        $status = $request->query('status');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');

        $query = Product::whereHas('centralPurchases', function (Builder $query) use ($startDate, $endDate) {
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        })->with(['productCategory', 'productSubcategory', 'centralPurchases' => function ($query) use ($supplier, $shipment, $status) {
            $query->with(['supplier']);
            if ($supplier !== '' && $supplier !== null) {
                $query->where('supplier_id', $supplier);
            }

            // if ($shipment !== '' && $shipment !== null) {
            //     $query->where('shipment_id', $shipment);
            // }

            // if ($status !== '' && $status !== null) {
            //     $query->where('status', $status);
            // }
        }]);

        $purchases = $query->get()
            ->map(function ($product, $key) {
                $totalQuantity = collect($product->centralPurchases)->sum(function ($item) {
                    return $item->pivot->quantity;
                });
                $totalAmount = collect($product->centralPurchases)->sum(function ($item) {
                    return $item->pivot->quantity * $item->pivot->price;
                });
                $avaregePrice = collect($product->centralPurchases)->average(function ($item) {
                    return $item->pivot->price;
                });
                return [
                    'category' => $product->productCategory->name,
                    'subcategory' => $product->productSubcategory->name,
                    'name' => $product->name,
                    'quantity' => $totalQuantity,
                    'amount' => $totalAmount,
                    'avg_price' => $avaregePrice,
                ];
            })
            ->groupBy('category');

        $html = '';
        $totalColumn = 5;

        if (count($purchases) > 0) {
            foreach ($purchases as $category => $products) {
                $html .= '<tr>';
                $html .= '<td>' . $category . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '</tr>';
                $totalAmountByCategory = 0;

                foreach ($products as $product) {
                    $html .= '<tr>';
                    $html .= '<td></td>';
                    $html .= '<td>' . $product['name'] . '</td>';
                    $html .= '<td>' . number_format($product['quantity']) . '</td>';
                    $html .= '<td class="text-right">' . number_format($product['amount']) . '</td>';
                    $html .= '<td class="text-right">' . number_format($product['avg_price'])  . '</td>';
                    $html .= '</tr>';
                    $totalAmountByCategory += $product['amount'];
                }

                $html .= '<tr>';
                $html .= '<td>Total For' . $category  . '</td>';
                $html .= '<td></td>';
                $html .= '<td></td>';
                $html .= '<td class="text-right">' . number_format($totalAmountByCategory)  . '</td>';
                $html .= '<td></td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr><td class="text-center" colspan="' . $totalColumn . '"> <em>Tidak ada data</em> </td></tr>';
        }

        return $html;
    }
    
        public function centralPurchaseHutangByCustomerSummay(){
        $centralSales = CentralPurchase::with(['supplier'])->get()
        ->filter(function ($sale) {
            $totalPayment = collect($sale->centralPurchaseTransactions)->where('payment_method', '!=', 'hutang')->sum('amount');
            $sale['total_payment'] = $totalPayment;

            $currentDate = date('Y-m-d');
            $invoiceDate = date('Y-m-d', strtotime($sale->date));

            $diffDays = Carbon::parse($currentDate)->diffInDays($invoiceDate);

            $dueGroup = '0-30';

            if ($diffDays <= 30) {
                $dueGroup = '0-30';
            } else if ($diffDays > 30 && $diffDays <= 60) {
                $dueGroup = '31-60';
            } else if ($diffDays > 60 && $diffDays <= 90) {
                $dueGroup = '61-90';
            } else {
                $dueGroup = '90+';
            }

            $sale['due_group'] = $dueGroup;

            // if ($customer->name!==null){

            // }else{
                
            // }

            return $sale->netto > $totalPayment;
        })
        ->values()
        ->groupBy([function ($sale) {
        if ($sale->supplier!==null){
        return $sale->supplier->name; 
        }else{
            return '';
        }
        }, 'due_group'])
        // Start:Remove from this to show detail
        ->map(function ($customers, $key) {
            return collect($customers)->map(function ($group, $key) {
                return 
                    collect($group)->values()->sum(function ($sale) {
                        return $sale->netto - $sale->total_payment;
                    });
                
            });
        })
        // End:Remove until this to show detail
        ->all();

        return view('report.supplier.summary.hutang', [
            "customers"=>$centralSales,
           
        ]);

    }

    public function centralPurchaseHutangBySupplierDetail(){
        $centralSales = CentralPurchase::with(['supplier'])->get()
        ->filter(function ($sale) {
            $totalPayment = collect($sale->centralPurchaseTransactions)->where('payment_method', '!=', 'hutang')->sum('amount');
            $sale['total_payment'] = $totalPayment;

            $currentDate = date('Y-m-d');
            $invoiceDate = date('Y-m-d', strtotime($sale->date));

            $diffDays = Carbon::parse($currentDate)->diffInDays($invoiceDate);

            $dueGroup = '0-30';

            if ($diffDays <= 30) {
                $dueGroup = '0-30';
            } else if ($diffDays > 30 && $diffDays <= 60) {
                $dueGroup = '31-60';
            } else if ($diffDays > 60 && $diffDays <= 90) {
                $dueGroup = '61-90';
            } else {
                $dueGroup = '90+';
            }

            $sale['due_group'] = $dueGroup;

            // if ($customer->name!==null){

            // }else{
                
            // }

            return $sale->netto > $totalPayment;
        })
        ->values()
        ->groupBy([function ($sale) {
        if ($sale->supplier!==null){
        return $sale->supplier->name; 
        }else{
            return '';
        }
        }, ])
        // Start:Remove from this to show detail
        // ->map(function ($customers, $key) {
        //     return collect($customers)->map(function ($group, $key) {
        //         return 
        //             collect($group)->values()->sum(function ($sale) {
        //                 return $sale->netto - $sale->total_payment;
        //             });
                
        //     });
        // })
        // End:Remove until this to show detail
        ->all();

        return view('report.supplier.detail.hutang', [
            "customers"=>$centralSales,
           
        ]);

       

    }
    public function centralPurchaseHutangByCustomerSummayExport(Request $request){
       
        return Excel::download(new CentralPurchaseHutangByCustomerSummayExport, 'hutang-supplier-summary.xlsx');
    }
    public function centralPurchaseHutangBySupplierDetailExport(Request $request){
       //return "tes";
        return Excel::download(new CentralPurchaseHutangBySupplierDetailExport, 'hutang-supplier-detail.xlsx');
    }
    
    public function piutangSummary()
    {
        $customers = CentralSale::with(['customer'])->get()
            ->filter(function ($sale) {
                $totalPayment = collect($sale->centralSaleTransactions)->where('payment_method', '!=', 'hutang')->sum('amount');
                $sale['total_payment'] = $totalPayment;

                $currentDate = date('Y-m-d');
                $invoiceDate = date('Y-m-d', strtotime($sale->date));

                $diffDays = Carbon::parse($currentDate)->diffInDays($invoiceDate);

                $dueGroup = '0-30';

                if ($diffDays <= 30) {
                    $dueGroup = '0-30';
                } else if ($diffDays > 30 && $diffDays <= 60) {
                    $dueGroup = '31-60';
                } else if ($diffDays > 60 && $diffDays <= 90) {
                    $dueGroup = '61-90';
                } else {
                    $dueGroup = '90+';
                }

                $sale['due_group'] = $dueGroup;

                // $customeName = 'unknown';

                // if($sale->customer !== null) {
                //     $customeName = $sale->customer->name;
                // }

                // $sale['customer_name'] = $customeName;

                return $sale->net_total > $totalPayment;
            })
            ->values()
            ->groupBy([function ($sale) {
                if ($sale->customer !== null) {
                    return $sale->customer->name;
                } else {
                    return 'unknown';
                }
            }, 'due_group'])
            // Start:Remove from this to show detail
            ->map(function ($customers, $key) {
                return collect($customers)->map(function ($group, $key) {
                    return collect($group)->values()->sum(function ($sale) {
                        return $sale->net_total - $sale->total_payment;
                    });
                });
            })
            // End:Remove until this to show detail
            ->all();

        return view('report.customers.summary.piutang', [
            'customers' => $customers,
        ]);
    }

    public function piutangDetail()
    {
        $customers = CentralSale::with(['customer'])->get()
            ->filter(function ($sale) {
                $totalPayment = collect($sale->centralSaleTransactions)->where('payment_method', '!=', 'hutang')->sum('amount');
                $sale['total_payment'] = $totalPayment;

                $currentDate = date('Y-m-d');
                $invoiceDate = date('Y-m-d', strtotime($sale->date));

                $diffDays = Carbon::parse($currentDate)->diffInDays($invoiceDate);

                $dueGroup = '0-30';

                if ($diffDays <= 30) {
                    $dueGroup = '0-30';
                } else if ($diffDays > 30 && $diffDays <= 60) {
                    $dueGroup = '31-60';
                } else if ($diffDays > 60 && $diffDays <= 90) {
                    $dueGroup = '61-90';
                } else {
                    $dueGroup = '90+';
                }

                $sale['due_group'] = $dueGroup;

                // $customeName = 'unknown';

                // if($sale->customer !== null) {
                //     $customeName = $sale->customer->name;
                // }

                // $sale['customer_name'] = $customeName;

                return $sale->net_total > $totalPayment;
            })
            ->values()
            ->groupBy([function ($sale) {
                if ($sale->customer !== null) {
                    return $sale->customer->name;
                } else {
                    return 'unknown';
                }
            }])
            // Start:Remove from this to show detail
            // ->map(function ($customers, $key) {
            //     return collect($customers)->map(function ($group, $key) {
            //         return collect($group)->values()->sum(function ($sale) {
            //             return $sale->net_total - $sale->total_payment;
            //         });
            //     });
            // })
            // End:Remove until this to show detail
            ->all();

        // return $customers;

        return view('report.customers.detail.piutang', [
            'customers' => $customers,
        ]);
    }
}
