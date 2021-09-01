<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadstockRelease extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
