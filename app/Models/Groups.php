<?php

namespace App\Models;

use App\Models\User;
use App\Models\Files;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Groups extends Model
{
    
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'groups_users')
            ->withPivot('is_admin')
            ->withTimestamps();
    }

    public function superAdmin()
    {
        return $this->users()->wherePivot('is_admin', 1);
    }
    
    public function files()
    {
        return $this->hasMany(Files::class);
    }
}
