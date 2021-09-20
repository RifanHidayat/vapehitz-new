<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioSale extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock', 'price', 'quantity', 'free');
    }

    public function studioSaleReturns()
    {
        return $this->hasMany(StudioSaleReturn::class);
    }
}
