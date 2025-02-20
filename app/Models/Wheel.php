<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wheel extends Model
{
    use HasFactory;

    // Define fillable fields if needed
    protected $fillable = ['game_id', /* other fields */];

     /**
     * Get the game that owns the wheel.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Get the clips associated with this wheel.
     */
    public function clips()
    {
        return $this->hasMany(WheelClip::class);
    }
}
