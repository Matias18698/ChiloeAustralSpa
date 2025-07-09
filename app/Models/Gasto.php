<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    // Nombre de la tabla (opcional si sigue la convención plural del nombre del modelo)
    protected $table = 'gastos';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'cotizacion',
        'centro_barco',
        'orden_compra',
        'fecha_oc',
        'hes',
        'fecha_hes',
        'valor_neto',
        'valor_facturado', 
        'empresa_facturar',
        'factura',
        'estado',
    ];

    // Si quieres que Laravel trate ciertas columnas como fechas
    protected $dates = [
        'fecha_oc',
        'fecha_hes',
        'created_at',
        'updated_at',
    ];

    // Puedes agregar métodos auxiliares, por ejemplo para verificar el estado

    public function isPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function isPagada()
    {
        return $this->estado === 'pagada';
    }
}
