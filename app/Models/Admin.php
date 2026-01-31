<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property mixed avatar
 */
class Admin extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;
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
    'name', 'email', 'password', 'type', 'status', 'created_by','username','parent_id','vat_country_code','vat_number','created_from'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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

}