<?php

namespace App\Exports;

use App\Models\CentralSale;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CentralSaleByProductDetailExport implements FromView, ShouldAutoSize, WithEvents
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
            $query->whereBetween(DB::raw('DATE(date)'), [$startDate, $endDate])->where('status', 'approved');
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

        $sales = $query->get();
        // ->groupBy(function ($item, $key) {
        //     return $item->code . ' - ' . $item->name;
        // })->all();

        return view('report.sales.detail.central-sale-by-product-sheet', [
            'sales_by_product' => $sales,
        ]);

        // return view('report.sheet.sales-order', [
        //     'sales_orders' => $salesOrders,
        //     'column_selections' => $columnSelections,
        //     'all_columns' => $this->allColumns,
        // ]);
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
   
                $event->sheet->getDelegate()->freezePane('A2');
   
            },
        ];
    }
}
