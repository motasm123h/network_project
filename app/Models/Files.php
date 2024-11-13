<?php

namespace App\Models;

use App\Models\Groups;
use App\Models\FilesBackUp;
use App\Models\file_reservation_logs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Files extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_name',
        'file_path',
        'group_id',
        'locked_by',
        'locked_at',
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class);
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function fileReservationLogs()
    {
        return $this->hasMany(file_reservation_logs::class);
    }

    public function BackUp(){
        return $this->hasMany(FilesBackUp::class);
    }
}
