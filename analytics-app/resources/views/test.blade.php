<h1>Prueba de Ventas</h1>

<table border="1" cellpadding="5">
    <tr>
        <th>Producto</th>
        <th>Categoría</th>
        <th>Total</th>
        <th>Fecha</th>
    </tr>

    @foreach ($ventas as $v)
        <tr>
            <td>{{ $v->producto }}</td>
            <td>{{ $v->categoria }}</td>
            <td>{{ $v->total }}</td>
            <td>{{ $v->fecha }}</td>
        </tr>
    @endforeach
</table>
