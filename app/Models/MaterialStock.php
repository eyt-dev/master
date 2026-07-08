<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialStock extends Model
{
    use HasFactory;

    protected $table = 'material_stocks';

    protected $fillable = [
        'stock_date',
        'name',
        'quantity',
        'supplier_id',
        'farm_id',
        'created_by',
    ];

    protected $casts = [
        'stock_date' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function supplier()
    {
        return $this->belongsTo(ChicksSupplier::class, 'supplier_id');
    }

    public function hangars()
    {
        return $this->hasMany(MaterialStockHangar::class, 'material_stock_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Many-to-many relationship with Hangar through MaterialStockHangar
    public function hangarsList()
    {
        return $this->belongsToMany(Hangar::class, 'material_stock_hangars', 'material_stock_id', 'hangar_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Get MaterialStockHangar allocations for this material stock
    public function materialStockHangarAllocations()
    {
        return $this->hasMany(MaterialStockHangar::class, 'material_stock_id');
    }
}
