<?php

namespace App\Http\Controllers;

use App\Imports\AskDatabasesImport;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\AskDatabases;

class UploadController extends Controller
{
    /*
     * Allowed File Type
     */
    protected $allowedFileType = ['application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    protected $data = null;

    public function __invoke(Request $request)
    {
        if ($request->has('filePost')) {
            $ip = $request->ip();
            $file = $request->file('filePost');

            AskDatabases::updateOrCreate(
                [],
                [
                    'name' => Str::replace('.', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . $ip),
                    'visitor' => $ip,
                ],
            );

            return $this->insertTableAnData($file, $ip);
        }

        return redirect('/')->with([
            'error' => 'Upload file faile',
            'status' => 'alert-danger',
        ]);
    }

    protected function insertTableAnData($file, $ip = '')
    {
        if (!$this->checkFileIsExcel($file)) {
            return redirect('/')->with([
                'error' => 'Your uploaded file is not an excel file',
                'status' => 'alert-danger',
            ]);
        }

        $i = 0;
        $nameTable = Str::replace('.', '', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . $ip);
        $data = Excel::toArray(new AskDatabasesImport(), $file);
        $columns = (new HeadingRowImport())->toArray($file);

        if ($i == 0) {
            $data = $data[$i];
            $columns = $columns[$i];
        }
        array_shift($data);
        return $this->createTableMysqlImport($nameTable, Arr::first($columns), $data, $ip);
    }

    protected function createTableMysqlImport(string $nameTable, array $columns, array $data, string $ip = '')
    {
        if ($columns) {
            $this->createTable($nameTable, $columns);
            $this->insertDataForTable($nameTable, $columns, $data);
            return redirect('/')->with([
                'error' => 'Upload file Success',
                'status' => 'alert-success',
            ]);
        }

        return redirect('/')->with([
            'error' => 'Data Null !!!',
            'status' => 'alert-danger',
        ]);
    }

    protected function createTable(string $nameTable, array $columns)
    {
        $intSchema = Schema::connection('mysql_import');
        $intSchema->dropIfExists($nameTable);
        $intSchema->create($nameTable, function (Blueprint $table) use ($columns) {
            foreach ($columns as $value) {
                if ($value == 'id') {
                    $table->integer('id');
                } else {
                    $table->longText($value);
                }
            }
        });
    }

    protected function insertDataForTable(string $nameTable, array $columns, array $data)
    {
        $connect = DB::connection('mysql_import');
        DB::beginTransaction();
        try {

            $data = $this->convertData($columns, $data);
            foreach ($data->chunk(10) as $values) {
                foreach ($values as $value) {
                    $connect->table($nameTable)->insert($value);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    protected function convertData(array $columns, array $data)
    {
        return collect($data)->map(function ($value, $key) use ($columns) {
            return array_combine($columns, $value);
        });
    }

    protected function checkFileIsExcel($file)
    {
        $extension = $file->getMimeType();
        if (in_array($extension, $this->allowedFileType)) {
            return true;
        }

        return false;
    }
}
