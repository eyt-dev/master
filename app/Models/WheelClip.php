<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WheelClip extends Model
{
    use HasFactory;

    protected $fillable = ['wheel_id', 'text'];

    /**
     * Get the wheel that owns this clip.
     */
    public function wheel()
    {
        return $this->belongsTo(Wheel::class);
    }

    /**
     * Get the game associated with this clip through the wheel.
     */
    public function game()
    {
        return $this->hasOneThrough(Game::class, Wheel::class, 'id', 'id', 'wheel_id', 'game_id');
    }
}
