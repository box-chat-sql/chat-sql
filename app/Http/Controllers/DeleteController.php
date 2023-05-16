<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AskDatabases;

class DeleteController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$request->has('query')) {
            return;
        }

        $ip = $request->ip();
        if ($request['query'][0] == 'all') {
            return $this->deleteAll($ip);
        }
        return $this->deleteSelected($request, $ip);
    }

    public function deleteAll(string $ip)
    {
        DB::beginTransaction();
        try {
            AskDatabases::query()
                ->where('visitor', $ip)
                ->delete();

            DB::commit();
            return [
                'data' => 'Delete successfully',
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'data' => 'Delete error',
            ];
        }
    }

    public function deleteSelected(Request $request, string $ip)
    {
        $query = AskDatabases::query()->where('visitor', $ip);
        $dataNew = [];
        $data = $query->pluck('data');
        foreach (Arr::first($data) as $key => $value) {
            if (!in_array($key, $request['query'])) {
                array_push($dataNew, $value);
            }
        }
        DB::beginTransaction();
        try {
            $dataConvert = convertData($dataNew);
            $table = $query->first();

            $table->data = $dataNew;
            $table->data_convert = $dataConvert;
            $table->save();

            $html = (string) view('table', ['data' => $dataNew]);
            DB::commit();

            return [
                'data' => 'Delete successfully',
                'html' => $html,
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'data' => 'Delete error',
            ];
        }
    }
}
?>
