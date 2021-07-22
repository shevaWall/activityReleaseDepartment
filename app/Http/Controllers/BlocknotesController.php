<?php

namespace App\Http\Controllers;

use App\Models\Blocknotes;
use Illuminate\Http\Request;

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
}
