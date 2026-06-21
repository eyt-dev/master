<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulationComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'formulation_id',
        'component_id',
        'component_type_id',
        'quantity',
        'price'
    ];

    public function formulation()
    {
        return $this->belongsTo(
            Formulation::class
        );
    }

    public function component()
    {
        return $this->belongsTo(
            Component::class
        );
    }

    public function componentType()
    {
        return $this->belongsTo(
            Form::class,
            'component_type_id'
        );
    }
}