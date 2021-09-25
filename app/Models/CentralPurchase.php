<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralPurchase extends Model
{
    use HasFactory;

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock', 'price', 'quantity','return_quantity','free');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function purchaseTransactions()
    {
        return $this->belongsToMany(PurchaseTransaction::class)->withPivot('amount');
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
