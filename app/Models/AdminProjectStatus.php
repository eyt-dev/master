<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProjectStatus extends Model
{
    protected $fillable = ['admin_id', 'project_id', 'status'];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
