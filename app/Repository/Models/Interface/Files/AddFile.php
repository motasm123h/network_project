<?php

namespace App\Repository\Models\Interface\Files;

use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;

interface AddFile
{
    public function addFile(FileRequest $request, int $user_id);
}
