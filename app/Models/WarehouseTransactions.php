<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransactions extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getTransactionAttribute($v){
        return json_decode($v, true);
    }

    public function setTransactionAttribute($v){
        $this->attributes['transaction'] = json_encode($v);
    }
}
