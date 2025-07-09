<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBitacorasTable extends Migration
{
    /**
     * Ejecutar las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacoras', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('zona')->nullable();
            $table->string('centro')->nullable();
            $table->string('numero_boleta')->nullable();

            $table->foreignId('trabajador_id')->constrained('trabajadores')->onDelete('cascade');
            $table->foreignId('embarcacion_id')->constrained('embarcaciones')->onDelete('cascade');
            
            // Horarios
            $table->time('hora_inicial')->nullable();
            $table->time('hora_final')->nullable();

            // Actividades y observaciones
            $table->text('actividades_am')->nullable();
            $table->text('actividades_pm')->nullable();
            $table->text('observaciones')->nullable();

            // Firma del encargado de faena
            $table->string('firma_encargado')->nullable(); // Ruta a la imagen de la firma
            $table->integer('total_jaulas')->default(0); // Total de jaulas
            $table->string('dimension_jaulas')->nullable(); 


            // Hasta 5 trabajadores con cargo buzo
            $table->foreignId('buzo_1_id')->nullable()->constrained('trabajadores')->onDelete('set null');
            $table->foreignId('buzo_2_id')->nullable()->constrained('trabajadores')->onDelete('set null');
            $table->foreignId('buzo_3_id')->nullable()->constrained('trabajadores')->onDelete('set null');
            $table->foreignId('buzo_4_id')->nullable()->constrained('trabajadores')->onDelete('set null');
            $table->foreignId('buzo_5_id')->nullable()->constrained('trabajadores')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Deshacer las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bitacoras');
    }
}
