<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groups_Users extends Model
{
    protected $table = 'groups_users';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'group_id',
        'is_admin',
    ];
}
