<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\AskDatabases;

class QueryController extends Controller
{
    public function __construct()
    {
    }

    public function __invoke(Request $request)
    {
        if ($request->has('query')) {
            try {
                return json_encode([
                    'status' => 'success',
                    'data' => $this->handleResponse($request),
                ]);
            } catch (\Throwable $th) {
                return json_encode([
                    'status' => 'error',
                    'data' => 'Sorry, There are currently no answers to this question. Thank !!!',
                ]);
            }
        }

        return json_encode([
            'status' => 'error',
            'data' => 'Sorry, There are currently no answers to this question. Thank !!!',
        ]);
    }

    public function handleResponse(Request $request)
    {
        $dataCookie = [];
        $question = [
            'type' => 'question',
            'time' => date('H:i:s d/m/Y',time()),
            'data' => $request['query'],
        ];
        array_push($dataCookie,$question);
        $query = DB::askQuery($request['query']);
        $data = 'Question: ' . $request['query'] . '<br>SQL query: ' . $query;
        
        $answer = [
            'type' => '',
            'time' => date('H:i:s d/m/Y',time()),
            'data' => $data,
        ];
        array_push($dataCookie,$answer);

        $getCookie = handleGetCookie('chatSQL');
        if ($getCookie) {
            $getDataCookie = Arr::get(json_decode($getCookie,true), 'data');
            $dataCookie = array_merge($getDataCookie,$dataCookie);
        }else{
            $dataCookie = $dataCookie;
        }
        handleSetCookie('chatSQL', json_encode(['data' => $dataCookie]));
        return $data;
    }
}
