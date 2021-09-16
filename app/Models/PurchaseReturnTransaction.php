<?php

namespace App\Models;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnTransaction extends Model
{

    use HasFactory;
    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }


}
