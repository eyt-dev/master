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

    public function materialStock()
    {
        return $this->belongsTo(MaterialStock::class, 'material_stock_id');
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }
}
