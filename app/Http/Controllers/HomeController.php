<?php

namespace App\Http\Controllers;

use App\Models\CentralSale;
use App\Models\RetailSale;
use App\Models\StudioSale;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $endDate = date("Y-m-d");
        $startDate = date('Y-m-d', (strtotime('-30 day', strtotime($endDate))));
        $centralSales = CentralSale::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($sale) {
                return date("Y-m-d", strtotime($sale->date));
            })->map(function ($sales) {
                return collect($sales)->where('status', 'approved')->sum('net_total');
            })->all();

        $retailSales = RetailSale::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($sale) {
                return date("Y-m-d", strtotime($sale->date));
            })->map(function ($sales) {
                return collect($sales)->sum('net_total');
            })->all();

        $studioSales = StudioSale::query()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($sale) {
                return date("Y-m-d", strtotime($sale->date));
            })->map(function ($sales) {
                return collect($sales)->sum('net_total');
            })->all();

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod(new DateTime($startDate), $interval, new DateTime(date('Y-m-d', (strtotime('+1 day', strtotime($endDate))))));

        // return $startDate;
        // return typeof($daterange);  
        $monthlyCentralSales = [];
        $monthlyRetailSales = [];
        $monthlyStudioSales = [];
        $dates = [];
        foreach ($daterange as $date) {
            array_push($dates, $date->format('d'));
            if (array_key_exists($date->format('Y-m-d'), $centralSales)) {
                array_push($monthlyCentralSales, $centralSales[$date->format('Y-m-d')]);
            } else {
                array_push($monthlyCentralSales, 0);
            }

            if (array_key_exists($date->format('Y-m-d'), $retailSales)) {
                array_push($monthlyRetailSales, $retailSales[$date->format('Y-m-d')]);
            } else {
                array_push($monthlyRetailSales, 0);
            }

            if (array_key_exists($date->format('Y-m-d'), $studioSales)) {
                array_push($monthlyStudioSales, $studioSales[$date->format('Y-m-d')]);
            } else {
                array_push($monthlyStudioSales, 0);
            }
        }

        $overdueInvoices = CentralSale::with(['customer', 'centralSaleTransactions'])
            ->where('due_date', '<', date("Y-m-d"))
            ->limit(5)
            ->get()
            ->each(function ($invoice) {
                $invoice['total_paid'] = $totalPaid = collect($invoice->centralSaleTransactions)->sum(function ($transaction) {
                    return $transaction->pivot->amount;
                });
            })
            ->filter(function ($invoice) {
                return $invoice['total_paid'] < $invoice->net_total;
            })->all();

        // return $sales;

        // return $monthlySales;
        // return $startDate;
        return view('dashboard.index', [
            'dates' => $dates,
            'central_sales' => $monthlyCentralSales,
            'retail_sales' => $monthlyRetailSales,
            'studio_sales' => $monthlyStudioSales,
            'overdue_invoices' => $overdueInvoices,
        ]);
    }
}
