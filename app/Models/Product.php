<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function productSubcategory()
    {
        return $this->belongsTo(ProductSubcategory::class);
    }

    public function centralPurchases()
    {
        return $this->belongsToMany(CentralPurchase::class)->withPivot('stock', 'price', 'quantity');
    }

    public function stockOpnames()
    {
        return $this->belongsToMany(StockOpname::class)->withPivot('good_stock', 'bad_stock', 'description');
    }

    public function stockOpnameRetails()
    {
        return $this->belongsToMany(StockOpnameRetail::class);
    }

    public function stockOpnameStudios()
    {
        return $this->belongsToMany(StockOpnameStudio::class);
    }

    public function centralSales()
    {
        return $this->belongsToMany(CentralSale::class)->withPivot('stock', 'booked', 'price', 'quantity', 'free', 'amount', 'editable');;
    }

    public function badstockRelease()
    {
        return $this->belongsToMany(BadstockRelease::class);
    }

    public function reqtoRetail()
    {
        return $this->belongsToMany(RequestToRetail::class);
    }

    public function reqRetailToCentral()
    {
        return $this->belongsToMany(RetailRequestToCentral::class);
    }

    public function centralSaleReturns()
    {
        return $this->belongsToMany(CentralSaleReturn::class)->withPivot('quantity', 'cause');
    }
}
