<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\CentralPurchase;
use App\Models\StudioSale;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionAccountExport implements FromView, ShouldAutoSize
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

    public function __construct($id,$startDate,$endDate)
    {
        $this->id = $id;
        $this->startDate=$startDate;
        $this->endDate=$endDate;
    }

    public function view(): View
    {
                $id=$this->id;
                $startDate= $this->startDate;
                $endtDate= $this->endDate;

      
        //return $startDate;
        
        $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);  
        $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
        ->findOrfail($id);
        $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
        $account=collect(Account::where('id','=',$id)->get())->each(function($account){
            $account['account_type'] = 'in';
            $account['note'] = 'Saldo Awal';
            $account['amount'] = $account['init_balance'];
        });
        $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
        ($centralPurchase){
            $centralPurchase['account_type'] = 'in';
            $centralPurchases['account_id']="1";
            $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
            $centralPurchase['description'] = $centralPurchase['note'];
            $centralPurchase['amount']=$centralPurchase['shipping_cost'];
        });

        //sale
        $studioSaleTransactions=Account::with('studioSaleTransactions')->find($id);
        $retailSaleTransactions=Account::with('retailSaleTransactions')->find($id);
        $centralSaleTransactions=Account::with('centralSaleTransactions')->find($id);
            

        //checked shpping account
        $id!="1"
        ? $transactionMerge = 
        ($account
        ->merge(collect($purchaseTransactions->purchaseTransactions)
           ->each(function($purchaseTransaction){
               $purchaseTransaction['description']=
               "pembayaran supplier dengan No. Order".$purchaseTransaction['code'];
           }))
        ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
           ->each(function($purchaseReturnTransaction){
               $purchaseReturnTransaction['description']=
                   "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
            }))
        ->merge(collect($centralSaleTransactions->centralSaleTransactions)
        ->each(function($centralSaleTransaction){
                $centralSaleTransaction['description']=
                    "Transaksi Penjualan pusat dengan No. Transaksi ".$centralSaleTransaction['code'];
                $centralSaleTransaction['amount']=abs($centralSaleTransaction['amount']);
             }))  
        ->merge(collect($studioSaleTransactions->studioSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                    "Transaksi Penjualan studio dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
             }))   
        ->merge(collect($retailSaleTransactions->retailSaleTransactions)
        ->each(function($studioSaleTransaction){
                $studioSaleTransaction['description']=
                "Transaksi Penjualan retail dengan No. Transaksi ".$studioSaleTransaction['code'];
                $studioSaleTransaction['amount']=abs($studioSaleTransaction['amount']);
        }))   
        ->merge($InOutTransactionAccount->accountTransactions))

        : $transactionMerge = $centralPurchases;

        // $accountTransactions=collect($transactionMerge)
        //     ->where('date','>=',$startDate,'AND','date','<=',$endtDate)
        //     ->sortBy('date')
        //     ->values()
        //     ->all();
        if (($startDate=='') && ($endtDate=='')){
            $accountTransactions=collect($transactionMerge)->sortBy('date')->values()->all();

        }else{
            $accountTransactions=collect($transactionMerge)
            ->whereBetween('date',[$startDate,$endtDate])
            ->sortBy('date')
            ->values()
            ->all();
        }
        
       

        return view('account.export', [
            'transactions' => $accountTransactions,
            'name'=>$purchaseTransactions->name,
            'number'=>$purchaseTransactions->number
        ]);

      
    }
}
