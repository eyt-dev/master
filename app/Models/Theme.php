<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
    ];

    /**
     * Get the admin who created the theme.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
