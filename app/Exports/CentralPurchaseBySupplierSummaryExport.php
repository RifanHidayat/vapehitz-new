<?php

namespace App\Exports;

use App\Models\CentralPurchase;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CentralPurchaseBySupplierSummaryExport implements FromView, ShouldAutoSize
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
        // $query = CentralSale::with(['products', 'supplier'])->whereBetween('date', [$startDate, $endDate]);
        // $query = CentralPurchase::with(['products' => function ($q) {
        //     $q->with(['productCategory', 'productSubcategory']);
        // }, 'supplier'])->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate]);
        // if ($supplier !== '' && $supplier !== null) {
        //     $query->where('supplier_id', $supplier);
        // }
        // if ($sortBy !== '' && $sortBy !== null) {
        //     $query->orderBy($sortBy, $sortIn);
        // }

        // $purchases = $query->get()

        // ->groupBy(function ($item, $key) {
        //     return $item->supplier->name;
        // })->all();
        // ->map(function ($purchase, $key) {
        //     $totalForSupplier = collect($purchase)->sum(function ($item) {
        //         $item->pivot->quantity * $item->pivot->amount;
        //     });
        //     return [
        //         'supplier' => $purchase->supplier->name,
        //         'total' => $totalForSupplier
        //     ];
        // })->all();
        // ->groupBy('supplier')->all();

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

        return view('report.purchases.summary.central-purchase-by-supplier-sheet', [
            'purchases_by_supplier' => $purchases,
        ]);
    }
}
