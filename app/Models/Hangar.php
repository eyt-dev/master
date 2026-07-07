<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hangar extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
        'name',
        'area_sqm',
        'layer_hens',
        'broiler_hens',
        'created_by',
    ];

    // Define Relationship with Farm
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    // Relationship with Flock through FlockHangar (pivot table)
    public function flocks()
    {
        return $this->belongsToMany(Flock::class, 'flock_hangars', 'hangar_id', 'flock_id')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Get FlockHangar allocations for this hangar
    public function flockAllocations()
    {
        return $this->hasMany(FlockHangar::class, 'hangar_id');
    }
}
