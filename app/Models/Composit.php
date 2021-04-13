<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getCompletedAttribute($v){
        return $v = ($v == 1) ? 'Готов' : 'Не готов';
    }

    /**
     * Получаем весь состав данного объекта
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function printableObject(){
        return $this->hasOne(PrintableObject::class, 'id', 'object_id');
    }

    public function formats(){
        return $this->hasOne(CountPdf::class, 'composit_id', 'id');
    }
}
