<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $table = 'user_devices';

    protected $fillable = [
        'user_id',
        'device_id',
        'fcm_token',
        'platform',
        'app_version',
        'is_active',
        'last_seen_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function refreshSessions()
    {
        return $this->hasMany(RefreshSession::class, 'device_id');
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'last_seen_at' => now(),
        ]);
    }

    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }
}
