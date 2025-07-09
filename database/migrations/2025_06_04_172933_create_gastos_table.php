<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGastosTable extends Migration
{
    public function up()
{
    Schema::create('gastos', function (Blueprint $table) {
        $table->id();
        $table->string('cotizacion')->nullable();
        $table->string('centro_barco')->nullable();
        $table->string('orden_compra')->nullable();
        $table->date('fecha_oc')->nullable();
        $table->string('hes')->nullable();
        $table->date('fecha_hes')->nullable();
        $table->decimal('valor_neto', 15, 2)->default(0);
        $table->decimal('valor_facturado', 15, 2)->default(0);
        $table->string('empresa_facturar')->nullable();
        $table->string('factura')->nullable();
        $table->enum('estado', ['pendiente', 'pagada'])->default('pendiente'); 
        $table->timestamps();
    });
}


public function down()
{
    Schema::dropIfExists('gastos');
}
    
}
