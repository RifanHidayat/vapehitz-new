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

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
                $id=$this->id;

                 //purchase transactions
                 $purchaseTransactions=Account::with('purchaseTransactions')->findOrfail($id);

                 //purchase rertun transactions      
                 $purchaseReturnTransactions=Account::with('purchaseReturnTransactions')
                 ->findOrfail($id);
         
                 //in out transaction
                 $InOutTransactionAccount=Account::with('accountTransactions')->find($id);
                 
                 //opening balance
                 $account=collect(Account::where('id','=',$id)->get())->each(function($account){
                     $account['account_type'] = 'in';
                     $account['Description'] = 'Saldo Awal';
                     $account['amount'] = $account['init_balance'];
                 });
                 
                 //supping cost
                 $centralPurchases=collect(CentralPurchase::where('shipping_cost','>',0,)->get())->each(function
                 ($centralPurchase){
                     $centralPurchase['account_type'] = 'in';
                     $centralPurchases['account_id']="1";
                     $centralPurchase['description'] = 'Biaya kirim Pembelian barang dengan No. Order'.$centralPurchase['code'];
                     $centralPurchase['note'] = $centralPurchase['note'];
                     $centralPurchase['amount']=$centralPurchase['shipping_cost'];
                 });
                 
                 
                
         
                 //checked shpping account
                 $id!="1"
                 ? $transactionMerge = 
                 ($account
                 ->merge(collect($purchaseTransactions->purchaseTransactions)
                    ->each(function($purchaseTransaction){
                        $purchaseTransaction['description']=
                        "Pembayaran retur dengan No. Transaksi".$purchaseTransaction['code'];
                    }))
                    
                 ->merge(collect($purchaseReturnTransactions->purchaseReturnTransactions)
                    ->each(function($purchaseReturnTransaction){
                        $purchaseReturnTransaction['description']=
                            "Pembayaran retur dengan No. Transaksi ".$purchaseReturnTransaction['code'];
                     }))
                 ->merge($InOutTransactionAccount->accountTransactions))
         
                 : $transactionMerge = $centralPurchases;
        
                $accountTransactions=collect($transactionMerge)->sortBy('date')->all();
       

        return view('account.export', [
            'transactions' => $accountTransactions,
            'name'=>$purchaseTransactions->name,
            'number'=>$purchaseTransactions->number
        ]);

      
    }
}
