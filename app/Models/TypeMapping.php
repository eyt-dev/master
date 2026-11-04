<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMapping extends Model
{
    use HasFactory;

    const PARENT_TYPES = [
        'Admin',
        'Public Vendor',
        'Private Vendor',
        'User',
    ];

    protected $fillable = [
        'project_id',
        'type',
        'url',
        'parent_type',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
