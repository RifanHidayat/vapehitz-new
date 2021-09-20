<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailSaleReturn extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'cause');
    }

    public function retailSale()
    {
        return $this->belongsTo(RetailSale::class);
    }
}
