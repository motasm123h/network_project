<?php

namespace App\Models;

use App\Models\User;
use App\Models\Files;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class file_reservation_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_id',
        'user_id',
        'action',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(Files::class);
    }
}
