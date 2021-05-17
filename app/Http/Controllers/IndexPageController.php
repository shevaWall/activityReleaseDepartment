<?php

namespace App\Http\Controllers;

use App\Models\PrintableObject;
use Illuminate\Http\Request;

class IndexPageController extends Controller
{
    public function index(){
        $latestObjects = PrintableObject::where('status_id', '!=', '4')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('index')
            ->with('latestObjects', $latestObjects);
    }
}
