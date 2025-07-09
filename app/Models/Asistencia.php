<?php

namespace App\Models;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias'; // Nombre de la tabla en la base de datos

    protected $fillable = ['trabajador_id','embarcacion_id', 'fecha', 'tipo_asistencia'];

    /**
     * Relación: Una asistencia pertenece a un trabajador.
     */
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id', 'id');
    }

    /**
     * Relación: Una asistencia pertenece a una embarcación.
     */
    public function embarcacion()
    {
        return $this->belongsTo(Trabajador::class, 'embarcacion_id', 'id');
    }



}
