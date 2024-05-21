<?php

namespace Modules\Client\Entities;
use Modules\Client\Entities\ClientImportModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ClientImportModel([
            'first_name'     => $row[0],
            'account_number'    => $row[1]
        ]);
    }
}