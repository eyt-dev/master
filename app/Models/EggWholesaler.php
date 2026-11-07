<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EggWholesaler extends Model
{
    use HasFactory;

    protected $table = 'egg_wholesalers';

    protected $fillable = [
        'name',
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
