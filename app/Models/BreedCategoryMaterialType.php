<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreedCategoryMaterialType extends Model
{
    use HasFactory;

    protected $table = 'breed_category_material_type';

    protected $fillable = [
        'breed_category',
        'material_type_id',
    ];

    public function materialType()
    {
        return $this->belongsTo(MaterialType::class, 'material_type_id');
    }
}
