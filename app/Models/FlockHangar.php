<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlockHangar extends Model
{
    use HasFactory;

    protected $fillable = [
        'flock_id',
        'hangar_id',
        'quantity',
    ];

    public function flock()
    {
        return $this->belongsTo(Flock::class, 'flock_id');
    }

    public function hangar()
    {
        return $this->belongsTo(Hangar::class, 'hangar_id');
    }
}
