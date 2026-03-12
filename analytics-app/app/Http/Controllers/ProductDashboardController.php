<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductDashboardController extends Controller
{

    public function index(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', '2024-01-01');
        $fechaFin    = $request->input('fecha_fin', now()->toDateString());
        $categoria   = $request->input('categoria');
        $proveedor   = $request->input('proveedor');

        $base = DB::table('ventas')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($categoria) {
            $base->where('categoria', $categoria);
        }
        if ($proveedor) {
            $base->where('proveedor', $proveedor);
        }

        $ventasPorProducto = (clone $base)
            ->select('producto', DB::raw('SUM(total) as total'))
            ->groupBy('producto')
            ->orderByDesc('total')
            ->get();

        $cantidadPorProducto = (clone $base)
            ->select('producto', DB::raw('SUM(cantidad) as cantidad'))
            ->groupBy('producto')
            ->orderByDesc('cantidad')
            ->get();

        $ventasPorCategoria = (clone $base)
            ->select('categoria', DB::raw('SUM(total) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        $ventasPorProveedor = (clone $base)
            ->select('proveedor', DB::raw('SUM(total) as total'))
            ->groupBy('proveedor')
            ->orderByDesc('total')
            ->get();

        $top10Productos = $ventasPorProducto->take(10);

        // Listas para selects
        $categorias = DB::table('ventas')->select('categoria')->distinct()->pluck('categoria');
        $proveedores = DB::table('ventas')->select('proveedor')->distinct()->pluck('proveedor');

        return view('dashboards.productos', compact(
            'fechaInicio','fechaFin','categoria','proveedor',
            'ventasPorProducto','cantidadPorProducto',
            'ventasPorCategoria','ventasPorProveedor','top10Productos',
            'categorias','proveedores'
        ));
    }

}
