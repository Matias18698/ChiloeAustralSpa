<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;
    protected $table = 'bitacoras'; // Nombre de la tabla en la base de datos
    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'fecha',
        'numero_boleta',
        'zona',
        'centro',
        'embarcacion_id',
        'trabajador_id',// es algun trabajador o encargado de la faena
        'hora_final',
        'hora_inicial',  
        'actividades_am',  // Actividades realizadas por la mañana
        'actividades_pm',  // Actividades realizadas por la tarde
        'firma_encargado', // Ruta a la imagen de la firma del encargado de faena
        'observaciones',    // Observaciones adicionales
        'total_jaulas', // Total de jaulas utilizadas
        'dimension_jaulas', // Dimensiones de las jaulas
        // Hasta 5 trabajadores con cargo buzo
        'buzo_1_id', // ID del primer buzo
        'buzo_2_id', // ID del segundo buzo
        'buzo_3_id', // ID del tercer buzo
        'buzo_4_id', // ID del cuarto buzo
        'buzo_5_id', // ID del quinto buzo
    ];

    /**
     * Relación con el modelo Embarcacion.
     */    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id', 'id');
    }

    /**
     * Relación: Una asistencia pertenece a una embarcación.
     */
    public function embarcacion()
    {
        return $this->belongsTo(Embarcacion::class, 'embarcacion_id', 'id');
    }

    /**
     * Formatear la fecha en un formato legible.
     */
    public function getFormattedFechaAttribute()
    {
        return \Carbon\Carbon::parse($this->fecha)->format('d/m/Y');
    }

    /**
     * Formatear las horas.
     */
    public function getFormattedHoraInicioAttribute()
    {
        return \Carbon\Carbon::parse($this->hora_inicio)->format('H:i') ?? 'N/A';
    }

    public function getFormattedHoraFinAttribute()
    {
        return \Carbon\Carbon::parse($this->hora_fin)->format('H:i') ?? 'N/A';
    }
    /**
     * Agregar una mutación para manejar la creación de las actividades de AM y PM.
     */
    public function setActividadesAmAttribute($value)
    {
        $this->attributes['actividades_am'] = json_encode($value);
    }

    public function getActividadesAmAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setActividadesPmAttribute($value)
    {
        $this->attributes['actividades_pm'] = json_encode($value);
    }

    public function getActividadesPmAttribute($value)
    {
        return json_decode($value, true);
    }

    public function buzo1() {
    return $this->belongsTo(Trabajador::class, 'buzo_1_id');
}

public function buzo2() {
    return $this->belongsTo(Trabajador::class, 'buzo_2_id');
}

public function buzo3() {
    return $this->belongsTo(Trabajador::class, 'buzo_3_id');
}

public function buzo4() {
    return $this->belongsTo(Trabajador::class, 'buzo_4_id');
}

public function buzo5() {
    return $this->belongsTo(Trabajador::class, 'buzo_5_id');
}

    /**
     * Escenario: Si tienes algún tipo de comportamiento especial o mutaciones personalizadas,
     * puedes 
     * crear funciones de "get" o "set" para esos campos.
     */
}
