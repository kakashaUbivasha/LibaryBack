<?php

namespace App\Imports;

use App\Models\Genre;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BookTagImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if(isset($row['book_id'])&&$row['book_id']!=''){
                DB::table('book_tag')->insert([
                    'book_id' => $row['book_id'],
                    'tag_id' => $row['tag_id'],
                ]);
            }
        }
    }
}
