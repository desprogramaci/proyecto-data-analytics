@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">Dashboard de Productos</h1>

    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">

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
        <label class="text-sm">Categoría</label>
        <select name="categoria" class="w-full border rounded px-2 py-1">
            <option value="">Todas</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat }}" @selected($categoria == $cat)>{{ $cat }}</option>
            @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm">Proveedor</label>
            <select name="proveedor" class="w-full border rounded px-2 py-1">
                <option value="">Todos</option>
                @foreach($proveedores as $prov)
                    <option value="{{ $prov }}" @selected($proveedor == $prov)>{{ $prov }}</option>
                @endforeach
            </select>
        </div>

        <div class="md:col-span-4">
            <button class="bg-slate-900 text-white px-4 py-2 rounded">
                Aplicar filtros
            </button>
        </div>

    </form>


    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por producto</h2>
            <div id="chartVentasProducto" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Cantidad por producto</h2>
            <div id="chartCantidadProducto" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por categoría</h2>
            <div id="chartVentasCategoriaProd" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4">
            <h2 class="text-sm font-semibold mb-2">Ventas por proveedor</h2>
            <div id="chartVentasProveedor" class="h-64"></div>
        </div>

        <div class="bg-white rounded shadow p-4 md:col-span-2">
            <h2 class="text-sm font-semibold mb-2">Top 10 productos</h2>
            <div id="chartTopProductos" class="h-64"></div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Ventas por producto
    new ApexCharts(document.querySelector("#chartVentasProducto"), {
        chart: { type: 'bar', height: 260 },
        series: [{
            name: 'Ventas',
            data: @json($ventasPorProducto->pluck('total'))
        }],
        xaxis: {
            categories: @json($ventasPorProducto->pluck('producto'))
        }
    }).render();

    // Cantidad por producto
    new ApexCharts(document.querySelector("#chartCantidadProducto"), {
        chart: { type: 'bar', height: 260 },
        series: [{
            name: 'Cantidad',
            data: @json($cantidadPorProducto->pluck('cantidad'))
        }],
        xaxis: {
            categories: @json($cantidadPorProducto->pluck('producto'))
        }
    }).render();

    // Ventas por categoría
    new ApexCharts(document.querySelector("#chartVentasCategoriaProd"), {
        chart: { type: 'pie', height: 260 },
        series: @json($ventasPorCategoria->pluck('total')),
        labels: @json($ventasPorCategoria->pluck('categoria'))
    }).render();

    // Ventas por proveedor
    new ApexCharts(document.querySelector("#chartVentasProveedor"), {
        chart: { type: 'donut', height: 260 },
        series: @json($ventasPorProveedor->pluck('total')),
        labels: @json($ventasPorProveedor->pluck('proveedor'))
    }).render();

    // Top 10 productos
    new ApexCharts(document.querySelector("#chartTopProductos"), {
        chart: { type: 'bar', height: 260 },
        series: [{
            name: 'Ventas',
            data: @json($top10Productos->pluck('total'))
        }],
        xaxis: {
            categories: @json($top10Productos->pluck('producto'))
        }
    }).render();
</script>
@endsection
