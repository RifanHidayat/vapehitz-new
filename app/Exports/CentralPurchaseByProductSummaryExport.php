<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CentralPurchaseByProductSummaryExport implements FromView, ShouldAutoSize
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
        $supplier = $this->request['supplier'];
        $shipment = $this->request['shipment'];
        $status = $this->request['status'];
        $sortBy = $this->request['sort_by'];
        $sortIn = $this->request['sort_in'];

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

        return view('report.purchases.summary.central-purchase-by-product-sheet', [
            'purchases_by_product' => $purchases,
        ]);
    }
}
