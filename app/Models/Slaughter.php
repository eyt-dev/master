<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slaughter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'contact_person',
        'mobile_number',
        'phone_code',
        'latitude',
        'longitude',
        'created_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
