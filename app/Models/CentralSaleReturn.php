<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralSaleReturn extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'cause');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function centralSale()
    {
        return $this->belongsTo(CentralSale::class);
    }

    public function centralSaleReturnTransactions()
    {
        return $this->belongsToMany(CentralSaleReturnTransaction::class)->withPivot('amount');
    }

    public function centralSaleTransactions()
    {
        return $this->hasMany(CentralSaleTransaction::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
