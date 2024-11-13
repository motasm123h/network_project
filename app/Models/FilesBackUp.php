<?php

namespace App\Models;

use App\Models\Files;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FilesBackUp extends Model
{
    protected $fillable = ['files_id', 'name', 'editor_name'];
    use HasFactory;

    public function file()
    {
        return $this->belongsTo(Files::class);
    }
}
