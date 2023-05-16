<?php

namespace App\Http\Controllers;

use App\Imports\AskDatabasesImport;
use App\Models\AskDatabases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UploadController extends Controller
{
    /*
     * Allowed File Type
     */
    protected $allowedFileType = ['text/csv', 'application/vnd.ms-excel', 'text/xls', 'text/xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

    public function __invoke(Request $request)
    {
        if ($request->has('filePost')) {
            $ip = $request->ip();
            $file = $request->file('filePost');

            return $this->insertTableAnData($file, $ip);
        }

        return redirect('/')->with([
            'error' => 'Upload file faile',
            'status' => 'alert-danger',
        ]);
    }

    protected function insertTableAnData($file, $ip = '')
    {
        if (! $this->checkFileIsExcel($file)) {
            return redirect('/')->with([
                'error' => 'Your uploaded file is not an excel file',
                'status' => 'alert-danger',
            ]);
        }

        $i = 0;
        $data = Excel::toArray(new AskDatabasesImport(), $file);
        if ($i == 0) {
            $data = $data[$i];
        }
        array_shift($data);

        return $this->createTableMysqlImport($data, $ip);
    }

    protected function createTableMysqlImport(array $data, string $ip = '')
    {
        if ($data) {
            $this->insertDataForTable($data, $ip);

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

    protected function insertDataForTable(array $data, string $ip)
    {
        if (! $data) {
            return;
        }
        DB::beginTransaction();
        try {
            $schema = $data[0][0];
            $dataConvert = convertData($data);
            AskDatabases::updateOrCreate(
                ['visitor' => $ip],
                [
                    'schema' => $schema,
                    'visitor' => $ip,
                    'data' => $data,
                    'data_convert' => $dataConvert,
                ],
            );
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    protected function checkFileIsExcel($file)
    {
        $mimeType = $file->getMimeType();
        if (in_array($mimeType, $this->allowedFileType)) {
            return true;
        }

        return false;
    }
}
