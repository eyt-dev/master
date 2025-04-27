<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_view_id',
        'name',
        'description',
        'web_image',
        'mobile_image',
        'primary_button_text',
        'primary_button_link',
        'secondary_button_text',
        'secondary_button_link',
        'created_by',
    ];

    /**
     * Get the store view associated with the slide.
     */
    public function storeView()
    {
        return $this->belongsTo(StoreView::class, 'store_view_id');
    }

    /**
     * Get the user who created the slide.
     */
    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
