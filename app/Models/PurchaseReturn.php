<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    public function centralPurchase()
    {
        return $this->belongsTo(CentralPurchase::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'cause');
    }
    
    public function supplier()
    {

        return $this->belongsTo(Supplier::class);
    }

    public function purchaseReturnTransactions()
    {

        return $this->hasMany(PurchaseReturnTransaction::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
   
}
