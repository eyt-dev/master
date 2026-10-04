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
        'material_type_id',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function materialType()
    {
        return $this->belongsTo(MaterialType::class, 'material_type_id');
    }

    public function getTypeAttribute()
    {
        return $this->materialType?->name ?? 'N/A';
    }
}
