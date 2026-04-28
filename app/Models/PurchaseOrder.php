<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'cantidad',
        'descripcion',
        'pedido_por',
        'detalles',
        'status', // pendiente, en_proceso, completado
        'attachment', // ruta del archivo subido por el proveedor (pdf/jpg/png)
        'approved_at', // timestamp cuando se aprobó (se subió factura)
        'spare_request_id', // relación a la solicitud que originó la orden
        'reject_reason', // motivo del rechazo por el gerente
    ];

    /**
     * Cast dates to Carbon instances so view helpers like ->locale() work.
     *
     * @var array
     */
    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Si la orden proviene de una solicitud, esta relación apunta a ella.
     */
    public function spareRequest()
    {
        return $this->belongsTo(SpareRequest::class, 'spare_request_id');
    }
}
