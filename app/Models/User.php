<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'login',
        'photo',
        'role_id',
        'password',
        'etat',
        'upload_failed'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
      //  'password',
     'created_at',
     'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    //    'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    function client() {
        return $this->hasOne(Client::class,'user_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function routeNotificationForSms($role){
        return '+221778170068';
      }

}
