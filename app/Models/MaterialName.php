<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialName extends Model
{
    use HasFactory;

    protected $table = 'material_names';

    protected $fillable = [
        'name',
    ];
}
