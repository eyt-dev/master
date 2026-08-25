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
        'type',
        'phone_code',
        'mobile_number',
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

    // Define Relationship with hangars
    public function hangars()
    {
        return $this->hasMany(Hangar::class, 'farm_id');
    }

    // Define Relationship with flocks
    public function flocks()
    {
        return $this->hasMany(Flock::class, 'farm_id');
    }

    public function getFullPhoneNumber(): ?string
    {
        if (!$this->mobile_number) {
            return null;
        }

        if ($this->phone_code) {
            return $this->phone_code . $this->mobile_number;
        }

        return $this->mobile_number;
    }
}
