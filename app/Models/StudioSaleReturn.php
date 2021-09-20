<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioSaleReturn extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'cause');
    }

    public function studioSale()
    {
        return $this->belongsTo(StudioSale::class);
    }
}
