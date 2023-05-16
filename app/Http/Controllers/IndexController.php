<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AskDatabases;

class IndexController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = AskDatabases::query()->where('visitor',$request->ip())->pluck('data');
        return view('index', ['data' => $data]);
    }
}
