<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_view_id',
        'logo',
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
        'domain',
        'title',
        'sub_title',
        'description',
        'created_by',
    ];

    public function storeView()
    {
        return $this->belongsTo(StoreView::class, 'store_view_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
