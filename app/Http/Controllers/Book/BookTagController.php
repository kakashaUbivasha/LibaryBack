<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Imports\BookTagImport;
use Maatwebsite\Excel\Facades\Excel;

class BookTagController extends Controller
{
    public function import(ImportRequest $request)
    {
        Excel::import(new BookTagImport, $request->file('file'));

        return response()->json(['message' => 'Импорт успешно завершён']);
    }
}
