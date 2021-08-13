<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['number', 'name', 'init_balance', 'type',];

    public function CentralPurchase()
    {
        return $this->hasMany(CentralPurchase::class);
    }

    public function AccountTransactions()
    {
        return $this->belongsTo(AccountTransaction::class);
    }
}
