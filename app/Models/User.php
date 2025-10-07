<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_uuid)) {
                $user->user_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_uuid',
        'username',
        'full_name',
        'email',
        'phone_number',
        'referral_code',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the hear about us record for this user.
     */
    public function hearAboutUs()
    {
        return $this->hasOne(HearAboutUs::class);
    }

    /**
     * Get all users this user has referred (as referrer).
     */
    public function referrals()
    {
        return $this->hasMany(ReferralRelationship::class, 'referrer_id');
    }

    /**
     * Get the referral relationship where this user was referred (as referee).
     */
    public function referredBy()
    {
        return $this->hasOne(ReferralRelationship::class, 'referred_id');
    }

    /**
     * Get all users this user has referred.
     */
    public function referredUsers()
    {
        return $this->hasManyThrough(
            User::class,
            ReferralRelationship::class,
            'referrer_id', // Foreign key on referral_relationships table
            'id', // Foreign key on users table
            'id', // Local key on users table
            'referred_id' // Local key on referral_relationships table
        );
    }
}
