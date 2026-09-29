<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefreshSession extends Model
{
    use HasFactory;

    protected $table = 'refresh_sessions';

    protected $fillable = [
        'user_id',
        'device_id',
        'token',
        'expires_at',
        'is_revoked',
        'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_revoked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function device()
    {
        return $this->belongsTo(UserDevice::class, 'device_id');
    }

    public function isValid(): bool
    {
        return !$this->is_revoked && now()->isBefore($this->expires_at);
    }

    public function revoke(): void
    {
        $this->update([
            'is_revoked' => true,
            'revoked_at' => now(),
        ]);
    }
}
