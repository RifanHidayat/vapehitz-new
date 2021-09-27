<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['number', 'name', 'init_balance', 'type',];

    public function CentralPurchase()
    {
        return $this->hasMany(CentralPurchase::class);
    }

    public function CentralSale()
    {
        return $this->hasMany(CentralSale::class);
    }

    public function AccountTransactions()
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function centralSaleTransactions()
    {
        return $this->hasMany(CentralSaleTransaction::class);
    }

    public function retailSaleTransactions()
    {
        return $this->hasMany(RetailSaleTransaction::class);
    }

    public function studioSaleTransactions()
    {
        return $this->hasMany(StudioSaleTransaction::class);
    }

    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class);
    }

    public function purchaseReturnTransactions()
    {
        return $this->hasMany(PurchaseReturnTransaction::class);
    }
}
