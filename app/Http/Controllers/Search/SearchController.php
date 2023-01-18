<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
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
        if(isset($term)){

            $objs = DB::table('printable_objects')
                ->where('name', 'like', "%$term%")
                ->orWhere('nomerZayavki', $term)
                ->get();

            // если это аякс запрос
            if(isset($ajax)){
                $r = array();

                foreach($objs as $obj){
                    (!is_null($obj->nomerZayavki)) ?
                        $objName = $obj->name." (№$obj->nomerZayavki)":
                        $objName = $obj->name;

                    array_push($r, ['label' => $objName, 'url'=>route('objects.composit', $obj->id)]);
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
