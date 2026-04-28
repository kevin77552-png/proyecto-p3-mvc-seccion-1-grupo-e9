<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpareRequest extends Model
{
	use HasFactory;

	protected $fillable = [
		'cantidad',
		'descripcion',
		'detalles',
		'tipo',
		'inventory_item_id',
		'comentario',
		'almacenista_id',
		'status',
		'reject_reason',
	];

	/**
	 * Usuario que realizó la solicitud (almacenista)
	 */
	public function almacenista()
	{
		return $this->belongsTo(User::class, 'almacenista_id');
	}

	/**
	 * Relación opcional a la PurchaseOrder generada a partir de esta solicitud.
	 */
	public function purchaseOrder()
	{
		return $this->hasOne(PurchaseOrder::class, 'spare_request_id');
	}

	public function inventoryItem()
	{
		return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
	}
}

