<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TestVentasController extends Controller
{
    public function index()
    {
        $ventas = DB::table('ventas')->limit(10)->get();
        return $ventas;
    }
}