<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintableObject extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*
     * Получаем статус для объекта печати
     */
    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }
}
