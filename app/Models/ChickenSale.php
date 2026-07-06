<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChickenSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_date',
        'farm_id',
        'flock_id',
        'hangar_id',
        'slaughter_id',
        'quantity',
        'total_weight',
        'gross_weight',
        'no_of_cages',
        'no_of_birds',
        'net_weight',
        'avg_weight_per_bird',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id');
    }

    public function flock()
    {
        return $this->belongsTo(Flock::class, 'flock_id');
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }

    public function slaughter()
    {
        return $this->belongsTo(Slaughter::class, 'slaughter_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
