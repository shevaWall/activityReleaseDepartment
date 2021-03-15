<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintableObject extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Получаем статус для объекта печати
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status(){
        return $this->hasOne(Status::class, 'id', 'status_id');
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
}
