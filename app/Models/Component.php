<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'form_id', 'unit_id', 'type'];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function elements()
    {
        return $this->belongsToMany(Element::class)
            ->withPivot(['amount', 'element_unit_id']);
    }

    public function compoPrices()
    {
        return $this->hasMany(CompoPrice::class);
    }

}
