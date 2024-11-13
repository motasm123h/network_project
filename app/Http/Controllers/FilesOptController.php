<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Models\FilesLocks\FilesOperation;

class FilesOptController extends Controller
{
    private $repo;
    public function __construct()
    {
        return [
            $this->repo = new FilesOperation(),
        ];
    }

    public function lockFile(Request $request)
    {
        $atter = $request->validate([
            'ids' => ['required']
        ]);
        return $this->repo->lockFiles($atter['ids']);
    }

    public function unlockFile(Request $request)
    {
        $atter = $request->validate([
            'ids' => ['required']
        ]);
        return $this->repo->UnLockFiles($atter['ids']);
    }
    public function getLockedFilesByUser()
    {
        return $this->repo->getLockedFilesByUser();
    }
}
