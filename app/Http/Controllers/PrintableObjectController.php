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
     * @param PrintableObject $PrintableObject
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showObjectSettings(PrintableObject $PrintableObject)
    {
        return view('objects.showObjectSettings')
            ->with([
                'object' => $PrintableObject,
                'statuses' => Status::all(),
            ]);
    }

    /**
     * отображения списка объектов по статусу (при значении 5 - отображаются все записи)
     * @param Status $Status
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function withStatus(Status $Status)
    {
        if ($Status->id != 5) {
            $objs = PrintableObject::where('status_id', $Status->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $objs = PrintableObject::orderBy('created_at', 'desc')
                ->get();
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
     * @param PrintableObject $PrintableObject
     * @param Status $Status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeObject(PrintableObject $PrintableObject, Status $Status)
    {
        $PrintableObject->countPdf()->delete();
        $PrintableObject->composits()->delete();
        $PrintableObject->delete();

        return redirect()->route('objects.withStatus', $Status->id);
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
     * @param PrintableObject $PrintableObject
     * @param int $status_id - id статуса, на кт меняется
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ajaxChangeObjectStatus(PrintableObject $PrintableObject, int $status_id)
    {
        $PrintableObject->update([
            'status_id' => $status_id
        ]);
        return redirect()->back();
    }

    /**
     * выводит расход бумаги
     */
    public function showPaperConsumption(PrintableObject $PrintableObject)
    {
        /*
         * $a_formats['1 (2, 3)'] - для вывода по конкретному разделу (ПД/РД/ИИ соответственно)
         */
        $a_formats = array();

        foreach ($PrintableObject->composits()->get() as $composit) {
            foreach ($PrintableObject->countPdf()->get() as $countPdf) {
                if ($composit->id == $countPdf->composit_id) {
                    $multypler = 1;
                    // устанавливаем множитель в зависимости от настроек объекта
                    switch ($composit->compositGroup_id) {
                        case 1:
                            (!is_null($PrintableObject->count_pd)) ? $multypler = $PrintableObject->count_pd : $multypler = 1;
                            break;
                        case 2:
                            (!is_null($PrintableObject->count_rd)) ? $multypler = $PrintableObject->count_rd : $multypler = 1;
                            break;
                        case 3:
                            (!is_null($PrintableObject->count_ii)) ? $multypler = $PrintableObject->count_ii : $multypler = 1;
                            break;
                    }
                    foreach ($countPdf->formats as $formatName => $format) {
                        if (isset($a_formats[$composit->compositGroup_id][$formatName])) {
                            // есть ли у формата цвет/ЧБ и есть ли цвет/ЧБ в массиве?
                            if (isset($format->Colored)) {
                                if (isset($a_formats[$composit->compositGroup_id][$formatName]['Colored'])) {
                                    $a_formats[$composit->compositGroup_id][$formatName]['Colored'] += $format->Colored * $multypler;
                                    $a_formats[$composit->compositGroup_id][$formatName]['Colored_once'] += $format->Colored;
                                } else {
                                    $a_formats[$composit->compositGroup_id][$formatName]['Colored'] = $format->Colored * $multypler;
                                    $a_formats[$composit->compositGroup_id][$formatName]['Colored_once'] = $format->Colored;
                                }
                            }
                            if (isset($format->BW)) {
                                if (isset($a_formats[$composit->compositGroup_id][$formatName]['BW'])) {
                                    $a_formats[$composit->compositGroup_id][$formatName]['BW'] += $format->BW * $multypler;
                                    $a_formats[$composit->compositGroup_id][$formatName]['BW_once'] += $format->BW;
                                } else {
                                    $a_formats[$composit->compositGroup_id][$formatName]['BW'] = $format->BW * $multypler;
                                    $a_formats[$composit->compositGroup_id][$formatName]['BW_once'] = $format->BW;
                                }
                            }
                        } else {
                            // есть ли у нового формата цвет/ЧБ?
                            if (isset($format->Colored)) {
                                $a_formats[$composit->compositGroup_id][$formatName]['Colored'] = $format->Colored * $multypler;
                                $a_formats[$composit->compositGroup_id][$formatName]['Colored_once'] = $format->Colored;
                            }
                            if (isset($format->BW)) {
                                $a_formats[$composit->compositGroup_id][$formatName]['BW'] = $format->BW * $multypler;
                                $a_formats[$composit->compositGroup_id][$formatName]['BW_once'] = $format->BW;
                            }
                        }
                    }
                }
            }
        }

        foreach($a_formats as $k => $v){
            ksort($a_formats[$k]);
        }

        $a_formats = $this->makePivotFormats($a_formats);

        return view('objects.showPaperConsumption')
            ->with([
                'object' => $PrintableObject,
                'formats' => $a_formats,
            ]);
    }

    /**
     * Собираем из ПД, РД, ИИ форматы и засовываем всё в одну общую для общих данных
     * @param array $formats - массив со всеми форматами
     * @return array
     */
    private function makePivotFormats(array $formats): array
    {
        foreach ($formats as $group_key => $group_val) {
            if ($group_key != 'formats') {
                foreach ($group_val as $format_key => $format_val) {
                    if(isset($format_val['Colored'])){
                        if (isset($formats['formats'][$format_key]['Colored'])) {
                            $formats['formats'][$format_key]['Colored'] += $format_val['Colored'];
                            $formats['formats'][$format_key]['Colored_once'] += $format_val['Colored_once'];
                        } else {
                            $formats['formats'][$format_key]['Colored'] = $format_val['Colored'];
                            $formats['formats'][$format_key]['Colored_once'] = $format_val['Colored_once'];
                        }
                    }

                    if(isset($format_val['BW'])){
                        if (isset($formats['formats'][$format_key]['BW'])) {
                            $formats['formats'][$format_key]['BW'] += $format_val['BW'];
                            $formats['formats'][$format_key]['BW_once'] += $format_val['BW_once'];
                        } else {
                            $formats['formats'][$format_key]['BW'] = $format_val['BW'];
                            $formats['formats'][$format_key]['BW_once'] = $format_val['BW_once'];
                        }
                    }
                }
            }
        }

        foreach($formats['formats'] as $format_key => $format_val){
            $c = $format_val['Colored'] ?? 0;
            $c_once = $format_val['Colored_once'] ?? 0;
            $bw = $format_val['BW'] ?? 0;
            $bw_once = $format_val['BW_once'] ?? 0;

            $formats['formats'][$format_key]['total'] = $c + $bw;
            $formats['formats'][$format_key]['total_once'] = $c_once + $bw_once;
        }
        return $formats;
    }
}
