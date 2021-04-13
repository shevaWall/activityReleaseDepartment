<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintableObject extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Получаем название статуса для объекта печати
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
    }

    /**
     * получаем все разделы данного объекта
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function composits(){
        return $this->hasMany(Composit::class, 'object_id', 'id');
    }

    /**
     * подмениваем для работы с чекбоксом
     * @param $v
     */
    public function setOriginalDocumentsAttribute($v){
        if(isset($v) && $v==1){
            $v = 1;
        }else{
            $v = 0;
        }
        $this->attributes['original_documents'] = $v;
    }

    public function countPdf(){
        return $this->hasManyThrough(
            CountPdf::class,
            Composit::class,
            'object_id',
            'composit_id',
            'id',
            'id'
        );
    }
}
