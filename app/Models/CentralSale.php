<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentralSale extends Model
{
    use HasFactory;
    protected $fillable = ['status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('stock', 'booked', 'price', 'quantity', 'free', 'amount', 'editable');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function centralSaleTransactions()
    {
        return $this->belongsToMany(CentralSaleTransaction::class)->withPivot('amount');
    }

    public function centralSaleReturns()
    {
        return $this->hasMany(CentralSaleReturn::class);
    }
}
