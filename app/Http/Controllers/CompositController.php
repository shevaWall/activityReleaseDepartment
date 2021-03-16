<?php

namespace App\Http\Controllers;

use App\Models\Composit;
use App\Models\PrintableObject;
use Illuminate\Http\Request;

class CompositController extends Controller
{
    /**
     * отображение состава объекта
     * @param int $object_id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(int $object_id){
        $printableObject = PrintableObject::findOrFail($object_id);
        $composits = Composit::where('object_id', $object_id)->with('printableObject')->get();
        return view('composit.index')
            ->with(
                [
                    'composits' => $composits,
                    'object' => $printableObject
                ]
            );
    }

    /**
     * обновление статуса раздела
     * @param int $composit_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(int $composit_id){
        Composit::findOrFail($composit_id)->changeStatus();
//        todo: сделать ajax

        return redirect()->back();
    }

    /**
     * ajax добавление раздела
     * @param Request $request
     */
    public function ajaxAddComposit(Request $request){
        $req = $request->all();
        $req['completed'] = 0;
        Composit::create($req);
        $this->refreshComposit();
    }

    /**
     * обновление состава после ajax добавления
     */
    private function refreshComposit(){
//       todo: сделать обновление вывода состава
    }
}
