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
        dump($composits);
        return view('composit.index')
            ->with(
                [
                    'composits' => $composits,
                    'object' => $printableObject
                ]
            );
    }

    public function updateStatus(int $composit_id){
        Composit::findOrFail($composit_id)->changeStatus();

//        return redirect()->back();
    }
}
