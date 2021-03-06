<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetailSaleTransaction extends Model
{
    use HasFactory;

    public function retailSales()
    {
        return $this->belongsToMany(RetailSale::class)->withPivot('amount');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
