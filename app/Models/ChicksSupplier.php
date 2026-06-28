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
        'address',
        'contact_person',
        'mobile_number',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
