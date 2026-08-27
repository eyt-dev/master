<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlockEnd extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'slaughter_id',
        'hangar_id',
        'sale_date',
        'cages_count',
        'cages_weight',
        'birds_per_cage',
        'total_birds_harvested',
        'available_birds',
        'remaining_birds',
        'total_weight',
        'avg_weight_per_bird',
        'notes',
        'ended_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'cages_weight' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'avg_weight_per_bird' => 'decimal:2',
    ];

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class);
    }

    public function slaughter()
    {
        return $this->belongsTo(Slaughter::class);
    }

    public function endedBy()
    {
        return $this->belongsTo(Admin::class, 'ended_by');
    }

    public function batchWeights()
    {
        return $this->hasMany(FlockEndDetail::class);
    }
}
