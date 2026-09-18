<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChicksSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'breed',
        'location',
        'latitude',
        'longitude',
        'phone_code',
        'contact_person',
        'mobile_number',
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
