<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    public function component()
    {
        return $this->hasMany(Component::class);
    }

    public function units(){
        return $this->belongsToMany(Unit::class);
    }
}
