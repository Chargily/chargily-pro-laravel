<?php

namespace Chargily\ChargilyProLaravel\Http\Controllers\Web;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class MainController extends Controller
{
    /**
     * Index
     *
     * @return void
     */
    public function index()
    {
        return new Response("OK", 200);
    }
}
