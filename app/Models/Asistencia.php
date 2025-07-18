<?php

namespace App\Models;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias'; // Nombre de la tabla en la base de datos

    protected $fillable = ['trabajador_id', 'fecha', 'tipo_asistencia'];

    /**
     * Relación: Una asistencia pertenece a un trabajador.
     */
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id', 'id');
    }




}
