<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CentralSaleByProductSummaryExport implements FromView, ShouldAutoSize
{
    private $request;
    private $allColumns = [
        [
            'id' => 'number',
            'text' => 'Nomor'
        ],
        [
            'id' => 'date',
            'text' => 'Tanggal'
        ],
        [
            'id' => 'customer',
            'text' => 'Customer'
        ],
        [
            'id' => 'po_number',
            'text' => 'Nomor PO'
        ],
        [
            'id' => 'po_date',
            'text' => 'Tanggal PO'
        ],
        [
            'id' => 'quotations',
            'text' => 'Quotation'
        ],
    ];

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // $columnSelections = explode(',', $this->request['columns']);
        $startDate = $this->request['start_date'];
        $endDate = $this->request['end_date'];
        $customer = $this->request['customer'];
        $shipment = $this->request['shipment'];
        $status = $this->request['status'];
        $sortBy = $this->request['sort_by'];
        $sortIn = $this->request['sort_in'];

        // $query = CentralSale::with(['products' => function ($q) {
        //     $q->with(['productCategory', 'productSubcategory']);
        // }, 'customer'])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        // if ($customer !== '' && $customer !== null) {
        //     $query->where('customer_id', $customer);
        // }

        // if ($shipment !== '' && $shipment !== null) {
        //     $query->where('shipment_id', $shipment);
        // }

        // if ($status !== '' && $status !== null) {
        //     $query->where('status', $status);
        // }

        // if ($sortBy !== '' && $sortBy !== null) {
        //     $query->orderBy($sortBy, $sortIn);
        // }

        // $sales = $query->get()->groupBy(function ($item, $key) {
        //     return $item->product->id . ' - ' . $item->product->name;
        // })->all();
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
            // ->groupBy(function ($item) {
            //     return $item->productCategory->name;
            // })
            // ->map(function ($item, $key) {
            //     $
            //     return [
            //         'key' => $key,
            //     ];
            // })

            // ->mapWithKeys(function ($item, $category) {
            //     $subcategoryGroup = collect($item)->values()->groupBy(function ($product) {
            //         return $product->productSubcategory->name;
            //     })->all();
            //     return [
            //         $category => $subcategoryGroup,
            //     ];
            // })
            ->all();
        // ->groupBy(function ($item, $key) {
        //     return $item->code . ' - ' . $item->name;
        // })->all();

        return view('report.sales.summary.central-sale-by-product-sheet', [
            'sales_by_product' => $sales,
        ]);

        // return view('report.sheet.sales-order', [
        //     'sales_orders' => $salesOrders,
        //     'column_selections' => $columnSelections,
        //     'all_columns' => $this->allColumns,
        // ]);
    }
}
