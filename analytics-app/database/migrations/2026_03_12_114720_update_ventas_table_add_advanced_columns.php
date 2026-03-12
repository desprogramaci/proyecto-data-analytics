<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('id_transaccion')->nullable()->unique()->after('id');
            $table->string('categoria')->nullable()->after('producto');
            $table->string('proveedor')->nullable()->after('categoria');
            $table->decimal('precio_unitario', 10, 2)->nullable()->after('cantidad');
            $table->decimal('descuento', 10, 2)->default(0)->after('precio_unitario');
            $table->decimal('impuesto', 10, 2)->default(0)->after('descuento');
            $table->decimal('total', 10, 2)->nullable()->after('impuesto');
            $table->string('canal_venta')->nullable()->after('fecha');
            $table->string('pais')->nullable()->after('canal_venta');
            $table->string('moneda')->nullable()->after('pais');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn([
                'id_transaccion',
                'categoria',
                'proveedor',
                'precio_unitario',
                'descuento',
                'impuesto',
                'total',
                'canal_venta',
                'pais',
                'moneda'
            ]);
        });
    }

};