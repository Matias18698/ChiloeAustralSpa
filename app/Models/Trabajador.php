<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    use HasFactory;

    protected $table = 'trabajadores'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'nombre',
        'apellido',
        'avatar',
        'rut',
        'fecha_nacimiento',
        'estado_civil',
        'nacionalidad',
        'direccion',
        'comuna',
        'email',
        'telefono',
        'afp',
        'cargo',
        'tamaño_ropa',
        'tipo_contrato',
        'sueldo_real',
        'sueldo_liquidacion',
        'user_id',
        'embarcacion_id',
    ];

    /**
     * Relación: Un trabajador pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     // Definir la relación con el modelo Asistencia
     public function asistencias()
     {
         return $this->hasMany(Asistencia::class, 'trabajador_id', 'id');
     }

     public function embarcacion()
    {
        return $this->belongsTo(Embarcacion::class, 'embarcacion_id', 'id');
    }

     public function bitacoras()
    {
        return $this->hasMany(Bitacora::class);
    }


}
