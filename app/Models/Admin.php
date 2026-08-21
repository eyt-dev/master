<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property mixed avatar
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable;
    const SUPER_ADMIN = 0;
    const ADMIN = 1;
    const PUBLIC_VENDOR = 2;
    const PRIVATE_VENDOR = 3;

    protected $guard_name = 'admin'; // 🔑 MUST MATCH GUARD
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'type', 'status', 'created_by', 'username', 'parent_id',
        'vat_country_code', 'vat_number', 'phone_code', 'created_from', 'url', 'project_id',
        'mobile_number', 'otp', 'otp_expires_at', 'otp_verified_at', 'notes', 'image', 'language', 'country_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'otp_verified_at' => 'datetime',
        'status' => 'string'
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function parent()
    {
        return $this->belongsTo(Admin::class, 'parent_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function projectStatuses()
    {
        return $this->hasMany(AdminProjectStatus::class);
    }

    public function syncProjectStatuses(array $rows): void
    {
        $this->projectStatuses()->delete();

        foreach ($rows as $row) {
            if (empty($row['project_id'])) {
                continue;
            }

            $this->projectStatuses()->create([
                'project_id' => $row['project_id'],
                'status' => $row['status'] ?? null,
            ]);
        }
    }
    // public function getProfileUrlAttribute()
    // {
    //     return asset('uploads/admins/'.$this->avatar);
    // }

    // public function setAvatarAttribute($value){
    //     if( $value ){
    //         // dd($value);
    //         $ext = $value->getClientOriginalExtension();
    //         $file_name = time().mt_rand( 1000, 9000 ) . '.' . $ext;
    //         $value->move( public_path( 'uploads/admins/' ), $file_name );
    //         $this->attributes['avatar'] =  $file_name;
    //     }
    // }

    // public function subscriptions()
    // {
    //     return $this->hasMany(Subscription::class);
    // }

    // public function vendors(){
    //     return $this->hasMany(User::class, 'user_id');
    // }

    // public function admins(){
    //     return $this->hasMany(User::class, 'user_id');
    // }

    // public function admin(){
    //     return $this->belongsTo( User::class, 'user_id' );
    // }
    public function setting(){
        return $this->hasOne( Setting::class, 'created_by' );
    }

    // public function groups(){
    //     return $this->hasMany( UCGroup::class, 'user_id' );
    // }

    // public function folders(){
    //     return $this->hasMany( Folder::class );
    // }

    // public function files(){
    //     return $this->hasMany( File::class );
    // }

    public function getRoleAttribute()
    {
        return $this->roles()->first()?->name; // returns role name like "SuperAdmin"
    }

    /**
     * Generate a 6-digit OTP and store it with 10-minute expiry.
     * Used for 2FA in Add2Farm APIs.
     *
     * @return string The generated OTP
     */
    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_verified_at' => null, // Reset verification status
        ]);

        return $otp;
    }

    /**
     * Check if the provided OTP is valid (not expired and matches).
     * Includes temporary 000000 override for development.
     *
     * @param string $providedOtp The OTP to verify
     * @return bool True if OTP is valid, false otherwise
     */
    public function isOtpValid(string $providedOtp): bool
    {
        // Temporary development override - remove after SMS integration
        if ($providedOtp === '000000') {
            return true;
        }

        // Check if OTP exists and hasn't expired
        if (!$this->otp || !$this->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($this->otp_expires_at)) {
            return false;
        }

        return hash_equals($this->otp, $providedOtp);
    }

    /**
     * Mark OTP as verified and clear the OTP.
     */
    public function markOtpVerified(): void
    {
        $this->update([
            'otp_verified_at' => now(),
            'otp' => null,
            'otp_expires_at' => null,
        ]);
    }

    /**
     * Check if OTP is expired.
     *
     * @return bool True if OTP has expired, false otherwise
     */
    public function isOtpExpired(): bool
    {
        return !$this->otp_expires_at || now()->isAfter($this->otp_expires_at);
    }

}