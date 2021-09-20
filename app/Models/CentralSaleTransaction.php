<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralSaleTransaction extends Model
{
    use HasFactory;

    public function centralSales()
    {
        return $this->belongsToMany(CentralSale::class)->withPivot('amount');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
