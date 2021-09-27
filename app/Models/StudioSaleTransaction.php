<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioSaleTransaction extends Model
{
    use HasFactory;

    public function studioSales()
    {
        return $this->belongsToMany(StudioSale::class)->withPivot('amount');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
