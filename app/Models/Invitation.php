<?php

namespace App\Models;

use App\Models\User;
use App\Models\Groups;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invitation extends Model
{
    use HasFactory;
    protected $fillable = ['group_id', 'sender_id', 'receiver_id', 'status'];

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public static function existingInvitation(int $group_id, $receiver_id, $status)
    {
        return self::where('group_id', $group_id)
            ->where('receiver_id', $receiver_id)
            ->where('status', $status)
            ->first();
    }
}
