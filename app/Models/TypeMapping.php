<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMapping extends Model
{
    use HasFactory;

    const PARENT_ADMIN = 'Admin';
    const PARENT_PUBLIC_VENDOR = 'Public Vendors';
    const PARENT_PRIVATE_VENDOR = 'Private Vendors';
    const PARENT_USER = 'Users';

    const PARENT_TYPES = [
        self::PARENT_ADMIN,
        self::PARENT_PUBLIC_VENDOR,
        self::PARENT_PRIVATE_VENDOR,
        self::PARENT_USER,
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

    /**
     * ID of the first mapping that belongs to the given parent type.
     * Used when an account is moved into a category (e.g. role upgrades).
     */
    public static function defaultIdForParent(string $parentType): ?int
    {
        return static::where('parent_type', $parentType)->orderBy('id')->value('id');
    }
}
