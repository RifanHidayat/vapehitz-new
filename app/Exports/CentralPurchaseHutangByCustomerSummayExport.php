<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\CentralPurchase;
use App\Models\Product;
use App\Models\StudioSale;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CentralPurchaseHutangByCustomerSummayExport implements FromView, ShouldAutoSize
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

    public function __construct()
    {
       
    }

    public function view(): View
    {
       
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
            return 'r';
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

        return view('report.supplier.summary.export', [
            "customers"=>$centralSales,
           
        ]);

      
    }
}
