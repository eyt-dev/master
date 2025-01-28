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
class User extends Authenticatable
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'address',
        // 'phone',
        // 'website',
        // 'avatar',
        // 'user_id',
        // 'group_id',
        // 'ugroup_id',
        // 'username',
        // 'access_table'
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
    ];

    // public function getProfileUrlAttribute()
    // {
    //     return asset('uploads/users/'.$this->avatar);
    // }

    // public function setAvatarAttribute($value){
    //     if( $value ){
    //         // dd($value);
    //         $ext = $value->getClientOriginalExtension();
    //         $file_name = time().mt_rand( 1000, 9000 ) . '.' . $ext;
    //         $value->move( public_path( 'uploads/users/' ), $file_name );
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
    // public function settings(){
    //     return $this->hasOne( Setting::class, 'created_by' );
    // }

    // public function groups(){
    //     return $this->hasMany( UCGroup::class, 'user_id' );
    // }

    // public function folders(){
    //     return $this->hasMany( Folder::class );
    // }

    // public function files(){
    //     return $this->hasMany( File::class );
    // }

}