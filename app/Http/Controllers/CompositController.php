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
     * отображение состава объекта
     * @param int $object_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(int $object_id){
        $object         = PrintableObject::findOrFail($object_id);
        $compositGroups = CompositGroup::all();
        $composits      = Composit::where('object_id', $object_id)->with('printableObject')->with('formats')->get();
        $persents       = $this->persent($composits, $compositGroups);

        return view('composit.index')
            ->with(
                [
                    'object'            => $object,
                    'compositGroups'    => $compositGroups,
                    'composits'         => $composits,
                    'persents'          => $persents,
                ]
            );
    }

    /**
     * ajax добавление раздела
     * @param Request $request
     */
    public function ajaxAddComposit(Request $request){
        $req = $request->all();
        $req['completed'] = 0;
        $addedComposit = Composit::create($req);
        return view('composit.newTr')
            ->with('composit', $addedComposit);
    }

    /**
     * ajax удаление раздела
     * @param int $composit_id
     */
    public function ajaxDeleteComposit(int $composit_id){
        CountPdf::where('composit_id', $composit_id)->delete();
        Composit::findOrFail($composit_id)->delete();
    }

    /**
     * ajax изменение статуса раздела
     * @param int $composit_id
     */
    public function ajaxChangeCompositStatus(int $composit_id){
        $composit = Composit::findOrFail($composit_id);

        ($composit->completed == 'Не готов') ? $composit->completed = 1 : $composit->completed = 0;
        $composit->save();
        echo $composit->completed;
    }

    /**
     * считает процентное соотношение между общим количеством разделов и готовыми разделами
     * @param object $composits - объект с разделами
     * @param object $compositGroups - объект с группами разделов
     * @return array
     */
    private final function persent(object $composits, object $compositGroups) {
        $groupPersents = [];

        foreach($compositGroups as $compositGroup){
            $countAll       = 0;
            $countCompleted = 0;
            foreach($composits as $composit){
                if($composit->compositGroup_id == $compositGroup->id){
                    $countAll++;
                    if($composit->completed == 'Готов'){
                        $countCompleted++;
                    }
                }
            }

            if(!($countAll == 0 || $countCompleted == 0)){
                $pers = $countCompleted/$countAll;
                $groupPersents[$compositGroup->id] = round($pers*100);
            }else{
                $groupPersents[$compositGroup->id] = 0;
            }
        }

        return $groupPersents;
    }

    /**
     * делает аякс запрос на переименовывание названия состава(раздела)
     * @param int $composit_id - id состава(раздела)
     */
    public function ajaxRenameComposit(Request $r, int $composit_id){
        Composit::findOrFail($composit_id)->update($r->all());
    }

}
