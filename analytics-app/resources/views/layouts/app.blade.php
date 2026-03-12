<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto Analytics</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- ApexCharts --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body class="bg-slate-100 text-slate-900">

    <nav class="bg-slate-900 text-white mb-6">
        <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">
            <span class="font-semibold">Proyecto Analytics</span>
            <div class="space-x-4 text-sm">
                <a href="{{ route('dashboard.ventas') }}" class="hover:underline">Ventas</a>
                <a href="{{ route('dashboard.productos') }}" class="hover:underline">Productos</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 pb-10">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
