<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningSaldoItem extends Model
{
    public function rekeningsaldo()
    {
        return $this->belongsTo(RekeningSaldo::class); 
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function items()
    {
        return $this->belongsTo(Transactionitem::class);
    }
}
