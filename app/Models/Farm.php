<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'number_of_hangars',
        'assigned_to',
        'created_by',
    ];

    // Define Relationship with assigned admin
    public function assignedAdmin()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
