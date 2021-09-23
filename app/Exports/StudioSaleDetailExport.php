<?php

namespace App\Exports;

use App\Models\StudioSale;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudioSaleDetailExport implements FromView, ShouldAutoSize
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
        // ->groupBy(function ($item, $key) {
        //     return $item->customer->name;
        // })->all();

        return view('report.sales.detail.studio-sale-sheet', [
            'sales' => $sales,
        ]);

        // return view('report.sheet.sales-order', [
        //     'sales_orders' => $salesOrders,
        //     'column_selections' => $columnSelections,
        //     'all_columns' => $this->allColumns,
        // ]);
    }
}
