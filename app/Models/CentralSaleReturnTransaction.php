<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralSaleReturnTransaction extends Model
{
    use HasFactory;

    public function centralSaleReturns()
    {
        return $this->belongsToMany(CentralSaleReturn::class)->withPivot('amount');
    }
}
