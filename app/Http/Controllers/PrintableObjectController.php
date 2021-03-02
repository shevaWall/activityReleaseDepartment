<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use Illuminate\Http\Request;

class PrintableObjectController extends Controller
{
    /**
     * отображение списка всех объектов
     */
    public function index(){
        return view('objects.index')
            ->with('objects', PrintableObject::all())
            ;
    }

    /**
     * Отображение формы для добавления нового объекта
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addNewObjectForm(){
        return view('objects.addNewObjectForm');
    }

    /**
     * обработчик добавления нового объекта
     * @param Request $request
     */
    public function addNewObjectSubmit(Request $request){
        dump($request);

//        todo: добавить свой валидатор Request и загрузку файлов

        $newObject = PrintableObject::create([
            'name'              =>  $request->name,
            'cipher'            =>  $request->cipher,
            'scan_img'          =>  $request->scan_img,
            'object_owner'      =>  $request->object_owner,
            'count_pd'          =>  $request->count_pd,
            'count_rd'          =>  $request->count_rd,
            'count_ii'          =>  $request->count_ii,
            'status_id'         =>  '1',
            'original_documents'=>  (!isset($request->original_documents)) ? '0' : '1',
        ]);

        dump($newObject);
    }
}
