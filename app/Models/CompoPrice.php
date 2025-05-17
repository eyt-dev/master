<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompoPrice extends Model
{
    use HasFactory;

    protected $fillable = ['component_id','element_id','pricing_date','price'];

    public function component()
    {
        return $this->belongsTo(Component::class);
    }
}
