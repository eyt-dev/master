<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialStockHangar extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_stock_id',
        'hangar_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function materialStock()
    {
        return $this->belongsTo(MaterialStock::class, 'material_stock_id');
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }

    public function scopeByMaterialType($query, $type = null)
    {
        if ($type === null || $type === 'feed' || $type === 'pelleted feed') {
            return $query->whereHas('materialStock.materialName.materialType', function($q) {
                $q->where('material_types.hangar_allocation', true);
            });
        }

        return $query->whereHas('materialStock.materialName.materialType', function($q) use ($type) {
            $q->where('material_types.value', strtolower(str_replace(' ', '_', $type)))
              ->orWhere('material_types.name', $type);
        });
    }
}
