<?php

namespace App\Models;

use App\Constants\NutritionType;
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

    public function elements()
    {
        return $this->belongsToMany(Element::class)
            ->withPivot('amount');
    }

 /*   public function getTypeAttribute($value)
    {
        $type = NutritionType::getNutritionType();

        return $type[$value] ?? '-';
    }*/


}
