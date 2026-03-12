<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentasDashboardController extends Controller
{

    public function index(Request $request)
    {
        // Filtros
        $fechaInicio = $request->input('fecha_inicio', '2024-01-01');
        $fechaFin    = $request->input('fecha_fin', now()->toDateString());
        $pais        = $request->input('pais');
        $canal       = $request->input('canal_venta');
        $categoria   = $request->input('categoria');

        // Base query
        $base = DB::table('ventas')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($pais) {
            $base->where('pais', $pais);
        }
        if ($canal) {
            $base->where('canal_venta', $canal);
        }
        if ($categoria) {
            $base->where('categoria', $categoria);
        }

        // KPIs
        $ventasTotales = (clone $base)->sum('total');
        $unidades      = (clone $base)->sum('cantidad');
        $ticketProm    = $unidades > 0 ? $ventasTotales / $unidades : 0;

        // Gráficos
        $ventasPorFecha = (clone $base)
            ->select('fecha', DB::raw('SUM(total) as total'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasPorPais = (clone $base)
            ->select('pais', DB::raw('SUM(total) as total'))
            ->groupBy('pais')
            ->orderByDesc('total')
            ->get();

        $ventasPorCanal = (clone $base)
            ->select('canal_venta', DB::raw('SUM(total) as total'))
            ->groupBy('canal_venta')
            ->orderByDesc('total')
            ->get();

        $ventasPorCategoria = (clone $base)
            ->select('categoria', DB::raw('SUM(total) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

            // KPIs básicos
        $ventasTotales = (clone $base)->sum('total');
        $unidades      = (clone $base)->sum('cantidad');
        $ticketProm    = $unidades > 0 ? $ventasTotales / $unidades : 0;

        // Margen estimado (30%)
        $margenEstimado = $ventasTotales * 0.30;
        $margenPorcentaje = 30;

        // Crecimiento mensual
        $mesActual = now()->format('Y-m');
        $mesAnterior = now()->subMonth()->format('Y-m');

        $ventasMesActual = DB::table('ventas')
            ->whereRaw("to_char(fecha, 'YYYY-MM') = ?", [$mesActual])
            ->sum('total');

        $ventasMesAnterior = DB::table('ventas')
            ->whereRaw("to_char(fecha, 'YYYY-MM') = ?", [$mesAnterior])
            ->sum('total');

        $crecimientoMensual = $ventasMesAnterior > 0
            ? (($ventasMesActual - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : 0;

        // Ticket promedio por categoría
        $ticketPorCategoria = DB::table('ventas')
            ->select('categoria', DB::raw('AVG(total) as ticket_promedio'))
            ->groupBy('categoria')
            ->orderByDesc('ticket_promedio')
            ->get();


        // Listas para selects
        $paises     = DB::table('ventas')->select('pais')->distinct()->pluck('pais');
        $canales    = DB::table('ventas')->select('canal_venta')->distinct()->pluck('canal_venta');
        $categorias = DB::table('ventas')->select('categoria')->distinct()->pluck('categoria');

        return view('dashboards.ventas', compact(
            'fechaInicio','fechaFin','pais','canal','categoria',
            'ventasTotales','unidades','ticketProm',
            'margenEstimado','margenPorcentaje',
            'ventasMesActual','ventasMesAnterior','crecimientoMensual',
            'ticketPorCategoria',
            'ventasPorFecha','ventasPorPais','ventasPorCanal','ventasPorCategoria',
            'paises','canales','categorias'
        ));

    }


}