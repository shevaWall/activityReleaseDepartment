<?php

namespace App\Http\Controllers;

use App\Models\Blocknotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BlocknotesController extends Controller
{
    // ajax добавление записки
    public function ajaxAddNote(Request $request){
        $note = Blocknotes::create($request->all());
        return view('blocknotes.newBlock')
            ->with('note', $note);
    }

    // ajax удаление записки
    public function ajaxDeleteNote(Request $r){
        Blocknotes::destroy($r->id);
    }

    // ajax изменение order_id при перемещении записок (jQuery sorted)
    public function ajaxChangeOrdersId(Request $r){
        DB::beginTransaction();
            foreach($r->all() as $noteId => $noteOrderId){
                Blocknotes::where('id', $noteId)
                    ->update(['order_id' => $noteOrderId]);
            }
        DB::commit();
    }

    // ajax изменение текста заметки
    public function ajaxChangeNoteName(Request $r, Blocknotes $Blocknotes){
        $Blocknotes->update($r->all());
    }
}
