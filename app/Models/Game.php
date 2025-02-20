<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 'games';

    protected $fillable = [
        'name',
        'type',
        'visibility',
        'display',
        'clips',
        'image',
        'created_by'
    ];

    /**
     * Get the game clips for the game.
     */
    public function clipData()
    {
        return $this->hasMany(GameClip::class, 'game_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     /**
     * Get all clips related to this game via wheels.
     */
    public function wheelClips()
    {
        return $this->hasManyThrough(WheelClip::class, Wheel::class, 'game_id', 'wheel_id');
    }
}
