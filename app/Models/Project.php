<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'description',
        'url',
    ];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'project_admin', 'project_id', 'admin_id')->withTimestamps();
    }

    public function userTypes()
    {
        return $this->hasMany(ProjectUserType::class);
    }
}
