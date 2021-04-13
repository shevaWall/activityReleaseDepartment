<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountPdf extends Model
{
    use HasFactory;

    protected $fillable = ['composit_id', 'formats'];

    public function formats(){
        $this->hasOne(Composit::class, 'id', 'composit_id');
    }

    public function getFormatsAttribute($value){
        return json_decode($value);
    }

}
