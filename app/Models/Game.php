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
}
