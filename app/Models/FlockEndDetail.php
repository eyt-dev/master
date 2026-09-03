<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlockEndDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_end_id',
        'batch_number',
        'gross_weight',
        'batch_weights',
    ];

    protected $casts = [
        'gross_weight' => 'decimal:2',
        'batch_weights' => 'array',
    ];

    public function flockEnd()
    {
        return $this->belongsTo(FlockEnd::class);
    }
}
