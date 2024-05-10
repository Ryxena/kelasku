<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;

    protected $fillable = [
        'name',
        'device_token',
        'school_id',
        'profile',
        'phone',
        'password',

    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendship', 'user_id', 'friend_id')->withPivot('status');
    }
    public function pendingFriendRequests(): BelongsToMany
    {
        return $this->friends()->wherePivot('status', 'pending');
    }

    public function acceptedFriends(): BelongsToMany
    {
        return $this->friends()->wherePivot('status', 'accepted');
    }
    public function pendingFriendRequestsSent(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendship', 'user_id', 'friend_id')
                    ->wherePivot('status', 'pending')
                    ->wherePivot('user_id', $this->id);
    }

    public function pendingFriendRequestsReceived(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendship', 'friend_id', 'user_id')
                    ->wherePivot('status', 'pending')
                    ->wherePivot('user_id', '!=', $this->id);
    }
    public function acceptedByOthers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'friendship', 'friend_id', 'user_id')
                    ->wherePivot('status', 'accepted');
    }
}
