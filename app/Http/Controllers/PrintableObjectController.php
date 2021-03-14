<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use App\Models\Status;
use Illuminate\Http\Request;

class PrintableObjectController extends Controller
{
    /**
     * отображение списка всех объектов
     */
    public function index(){
        $objects = PrintableObject::where('status_id', 1)->with('status')->get();

        return view('objects.index')
            ->with('objects', $objects)
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNewObjectSubmit(Request $request){
        /*
         * todo: добавить свой валидатор Request и загрузку файлов
         * как минимум проверка на уникальность связки полей "название" и "шифр". И если идентичные уже есть - не позволять создавать
         * */

        PrintableObject::create([
            'name'              =>  $request->name,
            'cipher'            =>  $request->cipher,
            'scan_img'          =>  $request->scan_img,
            'object_owner'      =>  $request->object_owner,
            'count_pd'          =>  $request->count_pd,
            'count_rd'          =>  $request->count_rd,
            'count_ii'          =>  $request->count_ii,
            'status_id'         =>  '1', // подефолту закидываем в 1, т.к. в планах Состояния: 1) в работе; 2) сдан; 3) на паузе; 4) удалён
            'original_documents'=>  (!isset($request->original_documents)) ? '0' : '1',
            'deadline'          =>  $request->deadline,
        ]);

        return redirect()->route('objects.index');
    }
}
