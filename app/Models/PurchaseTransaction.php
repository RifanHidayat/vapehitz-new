<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseTransaction extends Model
{
    use HasFactory;

    public function centralPurchases()
    {
        return $this->belongsToMany(CentralPurchase::class)->withPivot('amount');
    }
}
