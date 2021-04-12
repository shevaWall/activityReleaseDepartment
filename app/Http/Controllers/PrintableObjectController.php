<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrintableObjectController extends Controller
{
    /**
     * отображение списка всех объектов
     */
    public function index()
    {
        /*$test = new ShevawallPdf(Storage::path('tom2.pdf'));
        $test->convert2mm();
        $test->countFormats();*/



        $objs = PrintableObject::where('status_id', 1)
            ->with('status')
            ->with('composits')
            ->get();

        // считаем количество выполненных разделов в процентном соотношении к общему количеству разделов
        foreach($objs as $obj_k => $obj_v){
            $completed      = $objs[$obj_k]->composits->where('completed', "Готов")->count();
            $uncompleted    = $objs[$obj_k]->composits->where('completed', "Не готов")->count();
            if(!($completed === 0 && $uncompleted === 0)){
                $objs[$obj_k]->composits['persents'] = round($completed/($uncompleted+$completed) * 100);
            }else{
                $objs[$obj_k]->composits['persents'] = 0;
            }
        }

        return view('objects.index')
            ->with([
                'objects'   => $objs,
                'statuses'  => Status::all(),
            ]);
    }

    /**
     * Отображение формы для добавления нового объекта
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addNewObjectForm()
    {
        return view('objects.addNewObjectForm');
    }

    /**
     * обработчик добавления/изменения объекта
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNewObjectSubmit(Request $request)
    {
        /*
         * todo: добавить свой валидатор Request и загрузку файлов
         * как минимум проверка на уникальность связки полей "название" и "шифр". И если идентичные уже есть - не позволять создавать
         * */

        if (is_null($request->id)) {
            $req = $request->all();
            $req['status_id'] = 1;
            PrintableObject::create($req);
        } else {
            $req = $request->all();
            $req['original_documents'] = (!isset($req['original_documents'])) ? 0 : 1;
            PrintableObject::findOrFail($request->id)->update($req);
        }

        return redirect()->route('objects.index');
    }

    /**
     * изменение статуса объекта на "удалён
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function StatusDeletedObject(int $id)
    {
        PrintableObject::findOrFail($id)->update(['status_id' => 4]);

        return redirect()->route('objects.index');
    }

    /**
     * отображение настроек объекта
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showObjectSettings(int $id)
    {
//        todo: при редактировании уже существующего объекта в настройках должен быть выбор изменения статуса объекта (status_id)
        return view('objects.showObjectSettings')
            ->with('object', PrintableObject::findOrFail($id));
    }

    /**
     * отображение списка удалённых объектов
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function deletedObjects()
    {
        return view('objects.index')
            ->with([
                'objects' => PrintableObject::where('status_id', 4)->get(),
                'statuses'  => Status::all()
            ]);
    }

    /**
     * восстанавливает удалённый объект
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreObject(int $id)
    {
        PrintableObject::findOrFail($id)->update(
            [
                'status_id' => 1
            ]
        );

        return redirect()->route('objects.deleted');
    }

    /**
     * навсегда удаляет объект из БД
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeObject(int $id)
    {
        PrintableObject::findOrFail($id)->delete();
        return redirect()->route('objects.deleted');
    }

//    ajax изменение статуса объекта
    public function ajaxChangeObjectStatus(int $id){

    }
}
