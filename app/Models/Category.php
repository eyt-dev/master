<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_view_id',
        'title',
        'description',
        'image',
        'created_by',
    ];

    // Define Relationship with StoreView
    public function storeView()
    {
        return $this->belongsTo(StoreView::class, 'store_view_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
