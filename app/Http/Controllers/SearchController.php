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
        $ajax = $request->input('ajax');
        $error = 0;
        if(isset($term) && !is_null($term)){

            $objs = DB::table('printable_objects')
                ->where('name', 'like', "%$term%")
                ->orWhere('nomerZayavki', $term)
                ->get();

            // если это аякс запрос
            if(isset($ajax)){
                $r = array();

                foreach($objs as $obj){
                    array_push($r, ['label' => $obj->name, 'url'=>route('objects.composit', $obj->id)]);
                }

                return json_encode($r);
            }

            if($objs->count() > 0){
                return view('search.index')
                    ->with('objs', $objs);
            }else{
                return view('search.index')
                    ->with('noFound', "По вашему запросу '$term' не удалось ничего найти");
            }
        }else{
            return view('search.index')
                ->with('empty', "Ваш поисковый запрос пустой");
        }
    }
}
