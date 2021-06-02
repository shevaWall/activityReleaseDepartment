<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintableObjectController extends Controller
{
    /**
     * отображение списка всех объектов в работе
     */
    public function index()
    {
        $objs = PrintableObject::where('status_id', 1)
            ->with('status')
            ->with('composits')
            ->orderBy('created_at', 'desc')
            ->get();

        $objs = $this->countPercent($objs);

        return view('objects.index')
            ->with([
                'objects' => $objs,
                'statuses' => Status::all(),
            ]);
    }

    /**
     * Отображение формы для добавления нового объекта
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addNewObjectForm()
    {
        $maxNomerZayavki = DB::table('printable_objects')->max('nomerZayavki');

        if (is_null($maxNomerZayavki))
            $maxNomerZayavki = 0;

        return view('objects.addNewObjectForm')
            ->with('maxNomerZayavki', $maxNomerZayavki);
    }

    /**
     * обработчик добавления/изменения объекта
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNewObjectSubmit(Request $request)
    {
        /*
         * todo: добавить свой валидатор Request
         * проверка на заполненные поля
         * */
//        todo: сделать загрузку заявки на распечатку
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
     * отображение настроек объекта
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showObjectSettings(int $id)
    {
        return view('objects.showObjectSettings')
            ->with([
                'object' => PrintableObject::findOrFail($id),
                'statuses' => Status::all(),
            ]);
    }

    /**
     * отображения списка объектов по статусу (при значении 5 - отображаются все записи)
     * @param int $status_id
     */
    public function withStatus(int $status_id)
    {
        if ($status_id != 5) {
            $objs = PrintableObject::where('status_id', $status_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $objs = PrintableObject::orderBy('created_at', 'desc')->get();
        }

        $objs = $this->countPercent($objs);

        return view('objects.index')
            ->with([
                'objects' => $objs,
                'statuses' => Status::all()
            ]);


    }

    /**
     * навсегда удаляет объект из БД
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeObject(int $id, int $status_id)
    {
        $obj = PrintableObject::findOrFail($id);
        $obj->countPdf()->delete();
        $obj->composits()->delete();
        $obj->delete();

        return redirect()->route('objects.withStatus', $status_id);
    }

    /**
     * // считаем количество выполненных разделов в процентном соотношении к общему количеству разделов
     * @param $objs - массив объектов "объектов"
     * @return mixed
     */
    private function countPercent($objs)
    {
        foreach ($objs as $obj_k => $obj_v) {
            $completed = $objs[$obj_k]->composits->where('completed', "Готов")->count();
            $uncompleted = $objs[$obj_k]->composits->where('completed', "Не готов")->count();
            if (!($completed === 0 && $uncompleted === 0)) {
                $objs[$obj_k]->composits['persents'] = round($completed / ($uncompleted + $completed) * 100);
            } else {
                $objs[$obj_k]->composits['persents'] = 0;
            }
        }
        return $objs;
    }

    /**
     * ajax изменение статуса объекта
     * @param int $object_id - id объекта
     * @param int $status_id - id статуса, на кт меняется
     */
    public function ajaxChangeObjectStatus(int $object_id, int $status_id)
    {
        PrintableObject::where('id', $object_id)->update([
            'status_id' => $status_id
        ]);
        return redirect()->back();
    }

    /**
     * выводит общий расход бумаги
     */
    public function showPaperConsumption(int $object_id)
    {
        $obj = PrintableObject::where('id', $object_id)
            ->with('countPdf')
            ->with('composits')
            ->get();

        $a_formats = array();

//        todo: разделять на подсчет ПД/РД/ИИ отдельно и общий
        foreach ($obj[0]->composits as $composit) {
            foreach ($obj[0]->countPdf as $countPdf) {
                if ($composit->id == $countPdf->composit_id) {
                    $multypler = 1;
                    // устанавливаем множитель в зависимости от настроек объекта
                    switch ($composit->compositGroup_id) {
                        case 1:
                            (!is_null($obj[0]->count_pd)) ? $multypler = $obj[0]->count_pd : $multypler = 1;
                            break;
                        case 2:
                            (!is_null($obj[0]->count_rd)) ? $multypler = $obj[0]->count_rd : $multypler = 1;
                            break;
                        case 3:
                            (!is_null($obj[0]->count_ii)) ? $multypler = $obj[0]->count_ii : $multypler = 1;
                            break;
                    }
                    foreach ($countPdf->formats as $formatName => $format) {
                        // если такого формата нет - добавляем и инициализируем первые значения
                        if (!array_key_exists($formatName, $a_formats)) {
                            if (isset($format->Colored)) {
                                $a_formats[$formatName]['Colored'] = $format->Colored * $multypler;

                                (isset($a_formats[$formatName]['total']))
                                    ? $a_formats[$formatName]['total'] += $format->Colored * $multypler
                                    : $a_formats[$formatName]['total'] = $format->Colored * $multypler;
                            }
                            if (isset($format->BW)) {
                                $a_formats[$formatName]['BW'] = $format->BW * $multypler;

                                (isset($a_formats[$formatName]['total']))
                                    ? $a_formats[$formatName]['total'] += $format->BW * $multypler
                                    : $a_formats[$formatName]['total'] = $format->BW * $multypler;
                            }
                        } else {
                            // если такой формат уже есть, то складываем циферЬки
                            if (isset($format->Colored)) {
                                if (isset($a_formats[$formatName]['Colored'])) {
                                    $a_formats[$formatName]['Colored'] += ($format->Colored * $multypler);
                                } else {
                                    $a_formats[$formatName]['Colored'] = $format->Colored * $multypler;
                                }
                                $a_formats[$formatName]['total'] += ($format->Colored * $multypler);
                            }
                            if (isset($format->BW)) {
                                if (isset($a_formats[$formatName]['BW'])) {
                                    $a_formats[$formatName]['BW'] += ($format->BW * $multypler);
                                } else {
                                    $a_formats[$formatName]['BW'] = $format->BW * $multypler;
                                }
                                $a_formats[$formatName]['total'] += ($format->BW * $multypler);
                            }
                        }
                    }
                }
            }
        }

        ksort($a_formats);

        return view('objects.showPaperConsumption')
            ->with([
                'object' => $obj[0],
                'formats' => $a_formats,
            ]);
    }
}
