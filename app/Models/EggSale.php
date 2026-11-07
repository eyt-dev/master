<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EggSale extends Model
{
    use HasFactory;

    public const EGGS_PER_TRAY = 30;
    public const EGGS_PER_BOX = 360;

    protected $table = 'egg_sales';

    protected $fillable = [
        'sale_date',
        'farm_id',
        'flock_id',
        'egg_wholesaler_id',
        'tray',
        'total_eggs',
        'box',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'tray' => 'integer',
        'total_eggs' => 'integer',
        'box' => 'decimal:2',
    ];

    /**
     * Derive total eggs and boxes from the tray count.
     * total_eggs = tray × 30, box = total_eggs ÷ 360.
     */
    public static function calculate(int $tray): array
    {
        $totalEggs = $tray * self::EGGS_PER_TRAY;

        return [
            'total_eggs' => $totalEggs,
            'box' => round($totalEggs / self::EGGS_PER_BOX, 2),
        ];
    }

    /**
     * Eggs produced for a flock (sum of daily records "Eggs (Count)") minus eggs already sold.
     * Pass $exceptSaleId when editing so the sale's own eggs are not counted as sold.
     */
    public static function availableEggsForFlock(int $flockId, ?int $exceptSaleId = null): int
    {
        $produced = (int) DailyRecord::where('flock_id', $flockId)->sum('eggs_count');

        $sold = (int) self::where('flock_id', $flockId)
            ->when($exceptSaleId, fn ($q) => $q->where('id', '!=', $exceptSaleId))
            ->sum('total_eggs');

        return max(0, $produced - $sold);
    }

    /**
     * Stock per flock: Total = eggs produced (daily records), Remaining = Total minus eggs sold (never negative).
     * Returns [flock_id => ['total' => int, 'sold' => int, 'remaining' => int]].
     */
    public static function stockByFlock(iterable $flockIds): array
    {
        $flockIds = collect($flockIds)->unique()->values();

        $produced = DailyRecord::whereIn('flock_id', $flockIds)
            ->groupBy('flock_id')
            ->selectRaw('flock_id, SUM(eggs_count) as total')
            ->pluck('total', 'flock_id');

        $sold = self::whereIn('flock_id', $flockIds)
            ->groupBy('flock_id')
            ->selectRaw('flock_id, SUM(total_eggs) as total')
            ->pluck('total', 'flock_id');

        $stock = [];
        foreach ($flockIds as $flockId) {
            $total = (int) ($produced[$flockId] ?? 0);
            $soldEggs = (int) ($sold[$flockId] ?? 0);

            $stock[$flockId] = [
                'total' => $total,
                'sold' => $soldEggs,
                'remaining' => max(0, $total - $soldEggs),
            ];
        }

        return $stock;
    }

    /**
     * Error message when the requested trays need more eggs than are available; null when they fit.
     */
    public static function trayLimitMessage(int $tray, int $available): ?string
    {
        $needed = self::calculate($tray)['total_eggs'];

        if ($needed <= $available) {
            return null;
        }

        if ($available === 0) {
            return 'No eggs are available for this flock.';
        }

        $maxTray = intdiv($available, self::EGGS_PER_TRAY);
        $maxEggs = $maxTray * self::EGGS_PER_TRAY;

        return "Only {$available} eggs are available for this flock. Maximum is {$maxTray} tray(s) ({$maxEggs} eggs).";
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function flock()
    {
        return $this->belongsTo(Flock::class);
    }

    public function wholesaler()
    {
        return $this->belongsTo(EggWholesaler::class, 'egg_wholesaler_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
