<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralSale extends Model
{
    use HasFactory;

    public function customers()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
