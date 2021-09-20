<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailSale extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock', 'price', 'quantity', 'free');
    }

    public function retailSaleReturns()
    {
        return $this->hasMany(RetailSaleReturn::class);
    }
}
