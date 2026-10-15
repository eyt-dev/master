<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeRequest extends Model
{
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';

    protected $fillable = ['admin_id', 'status', 'approved_by', 'approved_at'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
