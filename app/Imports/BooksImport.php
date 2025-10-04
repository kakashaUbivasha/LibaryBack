<?php

namespace App\Imports;

use App\Models\Book;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Throwable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class BooksImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $processed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $index => $row) {
            $rowData = method_exists($row, 'toArray') ? $row->toArray() : (array) $row;

            if (!isset($rowData['title']) || trim($rowData['title']) === '') {
                $skipped++;
                Log::warning('Skipping book import row without title.', [
                    'row_index' => $index,
                    'row_data'  => $rowData,
                ]);
                continue;
            }

            try {
                Book::create([
                    'title'            => $rowData['title'],
                    'author'           => $rowData['author'] ?? null,
                    'description'      => $rowData['description'] ?? null,
                    'count'            => $rowData['count'] ?? 0,
                    'publication_date' => $rowData['publication_date'] ?? now(),
                    'isbn'             => $rowData['isbn'] ?? null,
                    'image'            => $rowData['image'] ?? null,
                    'genre_id'         => $rowData['genre_id'] ?? 1,
                    'language'         => $rowData['language'] ?? 'ru',
                ]);
                $processed++;
            } catch (Throwable $e) {
                $errors++;
                Log::error('Failed to import book row.', [
                    'row_index' => $index,
                    'row_data'  => $rowData,
                    'message'   => $e->getMessage(),
                ]);
            }
        }
        Log::info('Books import completed.', [
            'processed' => $processed,
            'skipped'   => $skipped,
            'errors'    => $errors,
        ]);
    }
}
