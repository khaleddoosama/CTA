<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function generateUserCode()
    {
        $userCode = Str::random(10); // Generate a random string of 10 characters
        $userCode = strtoupper($userCode); // Convert the string to uppercase
        // Check if the generated code already exists in the database
        while (User::where('code', $userCode)->exists()) {
            $userCode = Str::random(10); // If it exists, generate a new random code
        }

        return $userCode;
    }


    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->wallet()->create([
                'balance' => 0,
            ]);
        });
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // parent_id
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }


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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
