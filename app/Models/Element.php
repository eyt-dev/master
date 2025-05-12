<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'eu_code', 'synonym', 'attachment'];

    public function components()
    {
        return $this->belongsToMany(Component::class)
            ->withPivot('amount');
    }
}
