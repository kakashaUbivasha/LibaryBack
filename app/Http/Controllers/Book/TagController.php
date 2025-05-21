<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use App\Imports\TagImport;
use Maatwebsite\Excel\Facades\Excel;

class TagController extends Controller
{
    public function import(ImportRequest $request)
    {
        Excel::import(new TagImport, $request->file('file'));
        return response()->json(['message' => 'Импорт успешно завершён']);
    }
}
