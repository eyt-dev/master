<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'admin_domain',
        'dark_logo',
        'light_logo',
        'footer_logo',
        'favicon',
        'primary_text_color',
        'secondary_text_color',
        'primary_button_background',
        'secondary_button_background',
        'primary_button_text_color',
        'secondary_button_text_color',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function themes()
    {
        return $this->belongsTo(Theme::class, 'theme');
    }
}
