<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
class AskDatabasesImport implements ToCollection
{   
    public function __construct(){}

    /**
     * @param  array  $row
     * @return AskDatabases|null
     */
    public function collection(Collection $rows){}
}
