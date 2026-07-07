<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'chicks_supplier_id',
        'breed',
        'start_date',
        'total_quantity',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function chicksSupplier()
    {
        return $this->belongsTo(ChicksSupplier::class, 'chicks_supplier_id');
    }

    public function hangars()
    {
        return $this->hasMany(FlockHangar::class, 'flock_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Many-to-many relationship with Hangar through FlockHangar
    public function hangarsList()
    {
        return $this->belongsToMany(Hangar::class, 'flock_hangars', 'flock_id', 'hangar_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Get FlockHangar allocations for this flock
    public function flockHangarAllocations()
    {
        return $this->hasMany(FlockHangar::class, 'flock_id');
    }
}
