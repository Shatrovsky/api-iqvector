<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Middleware\DraftsOffMiddleware;

abstract class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware(DraftsOffMiddleware::class);
    }
}
