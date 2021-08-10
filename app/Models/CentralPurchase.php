<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralPurchase extends Model
{
    use HasFactory;

    public function Suppliers()
    {
        return $this->belongsTo(Supplier::class);
    }
}
