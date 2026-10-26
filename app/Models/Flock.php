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
     * Live birds per hangar: allocated - mortality (daily records) - harvested (flock ends).
     * Pass $exceptFlockEndId when editing a harvest so its own birds are not counted as sold.
     * Pass $exceptRecordDate when replacing a day's daily records so that day's mortality is not counted.
     */
    public function liveBirdsByHangar(?int $exceptFlockEndId = null, ?string $exceptRecordDate = null): array
    {
        // Sum per hangar so the result does not depend on the stored remaining_birds
        // of the latest row, which goes stale when an earlier harvest record is edited.
        $harvestedByHangar = FlockEnd::where('flock_id', $this->id)
            ->when($exceptFlockEndId, fn ($q) => $q->where('id', '!=', $exceptFlockEndId))
            ->selectRaw('hangar_id, SUM(total_birds_harvested) as harvested')
            ->groupBy('hangar_id')
            ->pluck('harvested', 'hangar_id');

        $mortalityByHangar = DailyRecord::where('flock_id', $this->id)
            ->when($exceptRecordDate, fn ($q) => $q->where('record_date', '!=', $exceptRecordDate))
            ->selectRaw('hangar_id, SUM(mortality) as mortality')
            ->groupBy('hangar_id')
            ->pluck('mortality', 'hangar_id');

        $liveBirds = [];
        foreach ($this->flockHangarAllocations as $allocation) {
            $harvested = (int) ($harvestedByHangar[$allocation->hangar_id] ?? 0);
            $mortality = (int) ($mortalityByHangar[$allocation->hangar_id] ?? 0);
            $liveBirds[$allocation->hangar_id] = max(0, $allocation->quantity - $mortality - $harvested);
        }

        return $liveBirds;
    }

    /**
     * Latest chicks weight per hangar: the most recent daily record with a weight (by date, then entry order).
     * A weight of 0 means the hangar was not weighed that day, so those rows are skipped.
     */
    public function latestChicksWeightByHangar(): array
    {
        $weights = [];

        $records = DailyRecord::where('flock_id', $this->id)
            ->where('chicks_weight', '>', 0)
            ->orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->get(['hangar_id', 'chicks_weight']);

        foreach ($records as $record) {
            $weights[$record->hangar_id] ??= (float) $record->chicks_weight;
        }

        return $weights;
    }

    /**
     * Flock average weight: sum of each hangar's latest weight divided by the number of weighed hangars.
     */
    public function averageLatestChicksWeight(): ?float
    {
        $weights = $this->latestChicksWeightByHangar();

        return $weights ? array_sum($weights) / count($weights) : null;
    }

    /**
     * Feed kg of the latest daily record before today per hangar (by date, then entry order).
     * Days without a record are skipped, so the most recent earlier record is used.
     */
    public function previousFeedKgByHangar(): array
    {
        $feeds = [];

        $records = DailyRecord::where('flock_id', $this->id)
            ->where('record_date', '<', today()->toDateString())
            ->orderBy('record_date', 'desc')
            ->orderBy('id', 'desc')
            ->get(['hangar_id', 'feed_kg']);

        foreach ($records as $record) {
            $feeds[$record->hangar_id] ??= (float) $record->feed_kg;
        }

        return $feeds;
    }

    /**
     * Feed per bird in grams per hangar: previous feed kg × 1000 ÷ the hangar's live birds today.
     */
    public function feedPerBirdByHangar(): array
    {
        $liveBirds = $this->liveBirdsByHangar();
        $previousFeed = $this->previousFeedKgByHangar();

        $result = [];
        foreach ($this->flockHangarAllocations as $allocation) {
            $hangarId = $allocation->hangar_id;
            $birds = $liveBirds[$hangarId] ?? 0;
            $result[$hangarId] = $birds > 0 ? round(($previousFeed[$hangarId] ?? 0) * 1000 / $birds, 2) : 0;
        }

        return $result;
    }

    /**
     * FCR per hangar as percentages:
     * act_fcr = chicks weight today ÷ feed per bird of the day before × 100.
     * trend_fcr = feed per bird of the day before ÷ chicks weight today × 100.
     */
    public function fcrByHangar(): array
    {
        $feedPerBird = $this->feedPerBirdByHangar();
        $weights = $this->latestChicksWeightByHangar();

        $result = [];
        foreach ($this->flockHangarAllocations as $allocation) {
            $hangarId = $allocation->hangar_id;
            $birds = $feedPerBird[$hangarId] ?? 0;
            $weight = $weights[$hangarId] ?? 0;

            $result[$hangarId] = [
                'act_fcr' => $birds > 0 && $weight > 0 ? round($weight / $birds * 100, 2) : 0,
                'trend_fcr' => $birds > 0 && $weight > 0 ? round($birds / $weight * 100, 2) : 0,
            ];
        }

        return $result;
    }

    /**
     * Sale status derived from harvest records and mortality.
     * Active: no harvest yet. Harvesting: harvest started, birds still alive and unsold.
     * Ended: no live birds left (all sold or dead). end_date is the last sale date.
     */
    public function saleStatus(): array
    {
        $remainingBirds = array_sum($this->liveBirdsByHangar());

        if ($remainingBirds == 0 && $this->flockHangarAllocations->sum('quantity') > 0) {
            $status = 'Ended';
            $endDate = $this->flockEnds()->latest('sale_date')->first()?->sale_date;
        } elseif (!$this->flockEnds()->exists()) {
            $status = 'Active';
            $endDate = null;
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
