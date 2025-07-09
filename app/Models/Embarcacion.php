<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embarcacion extends Model
{
    use HasFactory;

    protected $table = 'embarcaciones'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'nombre',
        'tipo',
        'patente',
        'capacidad',
        'estado',
        'latitud',
        'longitud',
        'imei',
    ];
    /**
     * Relación: Una embarcación puede tener muchos viajes.
     */
    /**
     * Relación: Una embarcación puede tener muchas asistencias.
     */
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
    /**
     * Relación: Una embarcación puede tener muchos trabajadores.
     */
    public function trabajadores()
{
    return $this->hasMany(Trabajador::class);
}


}

