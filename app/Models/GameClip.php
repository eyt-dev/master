<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameClip extends Model
{
    use HasFactory;

    protected $table = 'game_clip';

    protected $fillable = [
        'game_id',
        'text_length',
        'text_orientation',
        'color',
        'image'
    ];

    /**
     * Get the game that owns the clip.
     */
    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function wheelClips()
    {
        return $this->hasMany(WheelClip::class, 'game_clip_id');
    }
}
