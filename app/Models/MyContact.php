<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'formal_name',
        'vat_country_code',
        'vat_number',
        'email',
        'phone',
        'address1',
        'address2',
        'postal_code',
        'city',
        'image',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
