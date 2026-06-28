<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hangar extends Model
{
    use HasFactory;

    protected $fillable = [
        'farm_id',
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
}
