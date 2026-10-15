<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleChange extends Model
{
    const ACTION_UPGRADE = 'upgrade';
    const ACTION_DOWNGRADE = 'downgrade';
    const ACTION_APPROVE_UPGRADE = 'approve_upgrade';

    protected $fillable = [
        'admin_id', 'action', 'from_type', 'to_type', 'from_role', 'to_role',
        'upgrade_request_id', 'changed_by',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(Admin::class, 'changed_by');
    }

    public function upgradeRequest()
    {
        return $this->belongsTo(UpgradeRequest::class, 'upgrade_request_id');
    }
}
