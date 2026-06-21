<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Formulation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'formulation_code',
        'name',
        'target_animal',
        'inclusion_percentage',
        'total_volume',
        'indication_of_use',
        'reference',
        'template_id',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function template()
    {
        return $this->belongsTo(
            Formulation::class,
            'template_id'
        );
    }

    public function components()
    {
        return $this->hasMany(
            FormulationComponent::class
        );
    }

    public function analysis()
    {
        return $this->hasMany(
            FormulationAnalysis::class
        );
    }
}