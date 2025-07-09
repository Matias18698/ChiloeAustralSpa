<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trabajadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('avatar')->nullable();
            $table->string('rut')->unique();
            $table->date('fecha_nacimiento');
            $table->string('estado_civil');
            $table->string('nacionalidad');
            $table->string('direccion');
            $table->string('comuna');
            $table->string('email')->unique();
            $table->string('telefono');
            $table->string('afp');
            $table->string('cargo');
            $table->string('tamaño_ropa')->nullable();
            $table->string('tipo_contrato');
            $table->decimal('sueldo_real', 10, 2);
            $table->decimal('sueldo_liquidacion', 10, 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con usuarios
            $table->foreignId('embarcacion_id')->constrained('embarcaciones')->onDelete('cascade'); // Relación con embarcaciones



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadores');
    }
};
