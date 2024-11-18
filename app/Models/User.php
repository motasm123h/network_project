<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\file_reservation_logs;
use App\Models\Invitation;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'FCT',
        'role',
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

    public function groups()
    {
        return $this->belongsToMany(Groups::class, 'groups_users')
            ->withPivot('is_admin')
            ->withTimestamps();
    }

    public function isSuperAdminOfGroup($groupId)
    {
        return $this->groups()
            ->wherePivot('groups_id', $groupId)
            ->wherePivot('is_admin', 1)
            ->exists();
    }

    public function fileReservationLogs()
    {
        return $this->hasMany(file_reservation_logs::class);
    }

    public function sentInvitations()
    {
        return $this->hasMany(Invitation::class, 'sender_id');
    }

    public function receivedInvitations()
    {
        return $this->hasMany(Invitation::class, 'receiver_id');
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'receiver_id');
    }

    public static function usersNotInGroupOrInvited($groupId)
    {
        return self::whereDoesntHave('groups', function ($query) use ($groupId) {
            $query->where('groups.id', $groupId);
        })->whereDoesntHave('invitations', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->get();
    }
    public function receivedInvitationFormat($status)
    {
        return $this->receivedInvitations()
            ->where('status', $status)
            ->with(['group:id,name', 'sender:id,name,email'])
            ->get();
    }
    public function sentInvitationsFormat($status)
    {
        return $this->sentInvitations()
            ->where('status', $status)
            ->with(['group:id,name', 'receiver:id,name,email'])
            ->get();
    }
}
