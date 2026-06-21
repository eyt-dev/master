<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulationAnalysis extends Model
{
    use HasFactory;

    protected $table = 'formulation_analysis';

    protected $fillable = [
        'formulation_id',
        'element_name',
        'premix_value',
        'feed_value'
    ];

    public function formulation()
    {
        return $this->belongsTo(
            Formulation::class
        );
    }
}
