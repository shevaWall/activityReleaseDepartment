<?php

namespace App\Http\Controllers;

use App\Models\Composit;
use App\Models\CompositGroup;
use App\Models\CountPdf;
use App\Models\PrintableObject;
use Illuminate\Http\Request;
use PHPUnit\Framework\Constraint\Count;

class CompositController extends Controller
{
    /**
     * отображение состава (разделов) объекта
     * @param PrintableObject $PrintableObject - Route Model Binding
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(PrintableObject $PrintableObject)
    {
        $compositGroups = CompositGroup::all();
        $composits = Composit::where('object_id', $PrintableObject->id)->with('printableObject')->with('formats')->get();
        $persents = $this->persent($composits, $compositGroups);

        return view('composit.index')
            ->with([
                'object' => $PrintableObject,
                'compositGroups' => $compositGroups,
                'composits' => $composits,
                'persents' => $persents,
            ]);
    }

    /**
     * ajax добавление состава (раздела)
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function ajaxAddComposit(Request $request)
    {
        $req = $request->all();
        $req['completed'] = 0;
        $addedComposit = Composit::create($req);
        return view('composit.newTr')
            ->with('composit', $addedComposit);
    }

    /**
     * ajax удаление состава (раздела)
     * @param Composit $Composit - Route Model Binding
     */
    public function ajaxDeleteComposit(Composit $Composit)
    {
        CountPdf::where('composit_id', $Composit->id)->delete();
        $Composit->delete();
    }

    /**
     * ajax изменение статуса раздела
     * @param Composit $Composit - Route Model Binding
     */
    public function ajaxChangeCompositStatus(Composit $Composit)
    {
        ($Composit->completed == 'Не готов') ? $Composit->completed = 1 : $Composit->completed = 0;
        $Composit->save();
        echo $Composit->completed;
    }

    /**
     * считает процентное соотношение между общим количеством разделов и готовыми разделами
     * @param object $composits - объект с разделами
     * @param object $compositGroups - объект с группами разделов
     * @return array
     */
    private final function persent(object $composits, object $compositGroups)
    {
        $groupPersents = [];

        foreach ($compositGroups as $compositGroup) {
            $countAll = 0;
            $countCompleted = 0;
            foreach ($composits as $composit) {
                if ($composit->compositGroup_id == $compositGroup->id) {
                    $countAll++;
                    if ($composit->completed == 'Готов')
                        $countCompleted++;
                }
            }

            if (!($countAll == 0 || $countCompleted == 0)) {
                $pers = $countCompleted / $countAll;
                $groupPersents[$compositGroup->id] = round($pers * 100);
            } else
                $groupPersents[$compositGroup->id] = 0;
        }

        return $groupPersents;
    }

    /**
     * делает аякс запрос на переименовывание названия состава(раздела)
     * @param Request $r
     * @param Composit $Composit - Route Model Binding
     */
    public function ajaxRenameComposit(Request $r, Composit $Composit)
    {
        $Composit->update($r->all());
    }

}
