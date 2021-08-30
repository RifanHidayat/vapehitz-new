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

    public function centralSale()
    {
        return $this->belongsToMany(CentralSale::class);
    }

    public function badstockRelease()
    {
        return $this->belongsToMany(BadstockRelease::class);
    }

    public function reqtoRetail()
    {
        return $this->belongsToMany(ReqToRetail::class);
    }
}
