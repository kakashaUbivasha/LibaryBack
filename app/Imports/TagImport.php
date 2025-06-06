<?php

namespace App\Imports;

use App\Models\Tag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TagImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if(isset($row['name'])&&$row['name']!=''){
                Tag::firstOrCreate([
                    'name' => $row['name']
                ]);
            }
        }
    }
}
