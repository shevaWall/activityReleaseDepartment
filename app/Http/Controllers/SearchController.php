<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{

    /**
     * обработка поисковых запросов
     * @param Request $request - входящий запрос
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request){
        $term = $request->input('term');
        if(isset($term) && !is_null($term)){

            $objs = DB::table('printable_objects')
                ->where('name', 'like', "%$term%")
                ->orWhere('nomerZayavki', $term)
                ->get();

            if($objs->count() > 0){
                return view('search.index')
                    ->with('objs', $objs);
            }else{
                return view('search.index')
                    ->with('noFound', "По вашему запросу '$term' не удалоись ничего найти");
            }
        }else{
            return view('search.index')
                ->with('empty', "Ваш поисковый запрос пустой");
        }
    }
}
