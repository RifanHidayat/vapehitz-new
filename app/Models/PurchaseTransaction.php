<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    public function centralPurchases()
    {
        return $this->belongsToMany(CentralPurchase::class)->withPivot('amount');
    }

    public function supplier()
    {

        return $this->belongsTo(Supplier::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    // public function products()
    // {
    //     return $this->belongsToMany(Product::class)->withPivot('stock', 'price', 'quantity');
    // }

    

}
