<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'item_number',
        'sigicov',
        'descripcion',
        'unidad',
        'pasillo',
        'estante',
        'peldaño',
        'fecha',
        'realizado_por',
        'inventario',
        'resp_accesorios',
    ];

    /**
     * Ensure empty fecha values are stored as NULL to avoid invalid date errors on MySQL.
     */
    public function setFechaAttribute($value)
    {
        $this->attributes['fecha'] = $value ?: null;
    }

    /**
     * Accessor: devuelve `fecha` formateada como d/m/Y para las vistas.
     */
    public function getFechaAttribute($value)
    {
        if (! $value) return null;
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * Booted: asegurar que ciertos campos se guarden siempre en MAYÚSCULAS.
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $fields = ['sigicov', 'descripcion', 'unidad', 'pasillo', 'estante', 'peldaño', 'realizado_por', 'resp_accesorios'];
            foreach ($fields as $f) {
                // Sólo procesar si el atributo existe y es string
                if (array_key_exists($f, $model->attributes) && is_string($model->attributes[$f])) {
                    $model->attributes[$f] = mb_strtoupper($model->attributes[$f], 'UTF-8');
                }
            }
        });
    }
}
