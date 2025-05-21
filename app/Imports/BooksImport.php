<?php

namespace App\Imports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class BooksImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['title']) || trim($row['title']) === '') {
                continue;
            }

            Book::create([
                'title'            => $row['title'],
                'author'           => $row['author'] ?? null,
                'description'      => $row['description'] ?? null,
                'count'            => $row['count'] ?? 0,
                'publication_date' => $row['publication_date'] ?? now(),
                'isbn'             => $row['isbn'] ?? null,
                'image'            => $row['image'] ?? null,
                'genre_id'         => $row['genre_id'] ?? 1,
                'language'         => $row['language'] ?? 'ru',
            ]);
        }
    }
}
