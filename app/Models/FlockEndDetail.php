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
        'batch_weight',
    ];

    protected $casts = [
        'batch_weight' => 'decimal:2',
    ];

    public function flockEnd()
    {
        return $this->belongsTo(FlockEnd::class);
    }
}
