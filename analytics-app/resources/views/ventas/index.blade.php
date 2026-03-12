<h1>Listado de Ventas</h1>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Precio</th>
        <th>Fecha</th>
    </tr>

    @foreach($ventas as $venta)
    <tr>
        <td>{{ $venta->id }}</td>
        <td>{{ $venta->producto }}</td>
        <td>{{ $venta->cantidad }}</td>
        <td>{{ $venta->precio }}</td>
        <td>{{ $venta->fecha }}</td>
    </tr>
    @endforeach
</table>
