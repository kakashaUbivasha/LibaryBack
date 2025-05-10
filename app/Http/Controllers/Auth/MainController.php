<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\Service;

class MainController extends Controller
{
    public $service;
    public function __construct(Service $service){
        $this->service = $service;
    }
}
