<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flock extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function flockEnds()
    {
        return $this->hasMany(FlockEnd::class);
    }

    /**
     * Sale status derived from harvest records.
     * Active: no harvest yet. Harvesting: harvest started, birds still remaining.
     * Ended: all birds sold (remaining birds = 0). end_date is the last sale date.
     */
    public function saleStatus(): array
    {
        $remainingBirds = 0;
        foreach ($this->flockHangarAllocations as $allocation) {
            $lastHarvest = FlockEnd::where('flock_id', $this->id)
                ->where('hangar_id', $allocation->hangar_id)
                ->latest()
                ->first();

            $remainingBirds += $lastHarvest ? $lastHarvest->remaining_birds : $allocation->quantity;
        }

        if (!$this->flockEnds()->exists()) {
            $status = 'Active';
            $endDate = null;
        } elseif ($remainingBirds == 0) {
            $status = 'Ended';
            $endDate = $this->flockEnds()->latest('sale_date')->first()?->sale_date;
        } else {
            $status = 'Harvesting';
            $endDate = null;
        }

        return [
            'status' => $status,
            'end_date' => $endDate,
            'remaining_birds' => $remainingBirds,
        ];
    }

    /**
     * Each hangar is Inactive once all its allocated birds are sold; otherwise it stays Active.
     */
    public function syncHangarStatuses(): void
    {
        foreach ($this->flockHangarAllocations()->get() as $allocation) {
            $harvested = FlockEnd::where('flock_id', $this->id)
                ->where('hangar_id', $allocation->hangar_id)
                ->sum('total_birds_harvested');

            $status = $allocation->quantity - $harvested <= 0 ? 'Inactive' : 'Active';

            Hangar::where('id', $allocation->hangar_id)->update(['status' => $status]);
        }
    }
}
