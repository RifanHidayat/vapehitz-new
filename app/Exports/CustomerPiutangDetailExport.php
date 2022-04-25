<?php

namespace App\Exports;

use App\Models\CentralSale;
use App\Models\RetailSale;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerPiutangDetailExport implements FromView, ShouldAutoSize
{
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
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

        return view('report.customers.detail.piutang-sheet', [
            'customers' => $customers,
        ]);
    }
}
