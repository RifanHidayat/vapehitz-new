<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    use HasFactory;

    public function centralPurchases()
    {
        return $this->belongsToMany(CentralPurchase::class)->withPivot('quantity', 'cause');
    }
}
