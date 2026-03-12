@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Dashboard de Ventas</h1>

    {{-- Filtros simples de fecha --}}
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">

    <div>
        <label class="text-sm">Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
               class="w-full border rounded px-2 py-1">
    </div>

    <div>
        <label class="text-sm">Fecha fin</label>
        <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
               class="w-full border rounded px-2 py-1">
    </div>

    <div>
        <label class="text-sm">País</label>
        <select name="pais" class="w-full border rounded px-2 py-1">
            <option value="">Todos</option>
            @foreach($paises as $p)
                <option value="{{ $p }}" @selected($pais == $p)>{{ $p }}</option>
            @endforeach
        </select>
        </div>

        <div>
            <label class="text-sm">Canal</label>
            <select name="canal_venta" class="w-full border rounded px-2 py-1">
                <option value="">Todos</option>
                @foreach($canales as $c)
                    <option value="{{ $c }}" @selected($canal == $c)>{{ $c }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm">Categoría</label>
            <select name="categoria" class="w-full border rounded px-2 py-1">
                <option value="">Todas</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat }}" @selected($categoria == $cat)>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-5">
            <button class="bg-slate-900 text-white px-4 py-2 rounded">
                Aplicar filtros
            </button>
        </div>

    </form>


    {{-- KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-slate-500">Ventas totales</div>
            <div class="text-2xl font-semibold mt-1">
                € {{ number_format($ventasTotales, 2, ',', '.') }}
            </div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-slate-500">Unidades vendidas</div>
            <div class="text-2xl font-semibold mt-1">
                {{ $unidades }}
            </div>
        </div>
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs text-slate-500">Ticket promedio</div>
            <div class="text-2xl font-semibold mt-1">
                € {{ number_format($ticketProm, 2, ',', '.') }}
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-slate-500">Margen estimado</div>
        <div class="text-xl font-semibold mt-1">
            € {{ number_format($margenEstimado, 2, ',', '.') }}
        </div>
        <div class="text-xs text-slate-400">{{ $margenPorcentaje }}%</div>
    </div>

    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-slate-500">Crecimiento mensual</div>
        <div class="text-xl font-semibold mt-1">
            {{ number_format($crecimientoMensual, 2) }}%
        </div>
    </div>

    <div class="bg-white p-4 rounded shadow md:col-span-2">
        <div class="text-xs text-slate-500">Ticket promedio por categoría</div>
        <ul class="mt-2 text-sm">
            @foreach($ticketPorCategoria as $cat)
                <li class="flex justify-between border-b py-1">
                    <span>{{ $cat->categoria }}</span>
                    <span>€ {{ number_format($cat->ticket_promedio, 2, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>
    </div>

</div>


    {{-- Gráficos --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por fecha</h2>
            <div id="chartVentasFecha" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por país</h2>
            <div id="chartVentasPais" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por canal</h2>
            <div id="chartVentasCanal" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por categoría</h2>
            <div id="chartVentasCategoria" class="h-64"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Ventas por fecha
    new ApexCharts(document.querySelector("#chartVentasFecha"), {
        chart: { type: 'line', height: 260 },
        series: [{
            name: 'Ventas',
            data: @json($ventasPorFecha->pluck('total'))
        }],
        xaxis: {
            categories: @json($ventasPorFecha->pluck('fecha')),
            labels: { rotate: -45 }
        }
    }).render();

    // Ventas por país
    new ApexCharts(document.querySelector("#chartVentasPais"), {
        chart: { type: 'bar', height: 260 },
        series: [{
            name: 'Ventas',
            data: @json($ventasPorPais->pluck('total'))
        }],
        xaxis: {
            categories: @json($ventasPorPais->pluck('pais'))
        }
    }).render();

    // Ventas por canal
    new ApexCharts(document.querySelector("#chartVentasCanal"), {
        chart: { type: 'pie', height: 260 },
        series: @json($ventasPorCanal->pluck('total')),
        labels: @json($ventasPorCanal->pluck('canal_venta'))
    }).render();

    // Ventas por categoría
    new ApexCharts(document.querySelector("#chartVentasCategoria"), {
        chart: { type: 'donut', height: 260 },
        series: @json($ventasPorCategoria->pluck('total')),
        labels: @json($ventasPorCategoria->pluck('categoria'))
    }).render();
</script>
@endsection
