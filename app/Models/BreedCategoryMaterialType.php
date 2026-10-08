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

    /**
     * Get all material type IDs for a breed category
     */
    public static function getMaterialTypeIdsForBreed($breedCategory)
    {
        return self::where('breed_category', $breedCategory)
            ->pluck('material_type_id')
            ->toArray();
    }

    /**
     * Get all material names for a breed category
     */
    public static function getMaterialsForBreed($breedCategory)
    {
        $materialTypeIds = self::getMaterialTypeIdsForBreed($breedCategory);

        if (empty($materialTypeIds)) {
            return collect([]);
        }

        return MaterialName::whereIn('material_type_id', $materialTypeIds)
            ->with('materialType')
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if material type is allowed for breed category
     */
    public static function isMaterialTypeAllowedForBreed($breedCategory, $materialTypeId)
    {
        return self::where('breed_category', $breedCategory)
            ->where('material_type_id', $materialTypeId)
            ->exists();
    }
}
