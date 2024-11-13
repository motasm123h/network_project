<?php

namespace App\Repository\Models\Interface\Files;

use Illuminate\Support\Facades\Request;

interface LockUnLockFile
{
    public function LockFiles(array $file_id);
    public function UnLockFiles(array $file_ids);
    public function getLockedFilesByUser();
}
