<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function centralPurchases()
    {
        return $this->hasMany(CentralPurchase::class);
    }
    public function purchaseTransactions()
    {
        return $this->hasMany(PurchaseTransaction::class);
    }

}
