<?php

namespace App\Imports;

use App\Models\Genre;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GenresImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if(isset($row['name'])&&$row['name']!=''){
                Genre::firstOrCreate([
                    'name' => $row['name']
                ]);
            }
        }
    }
}
