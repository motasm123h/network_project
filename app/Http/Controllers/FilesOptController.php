<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Repository\Models\FilesLocks\FilesOperationService;

class FilesOptController extends Controller
{
    private $service;
    public function __construct()
    {
        return [
            $this->service = new FilesOperationService(),
        ];
    }

    public function lockFile(Request $request)
    {
        $atter = $request->validate([
            'ids' => ['required']
        ]);
        return $this->service->lockFiles($atter['ids']);
    }

    public function unlockFile(Request $request)
    {
        $atter = $request->validate([
            'ids' => ['required']
        ]);
        return $this->service->UnLockFiles($atter['ids']);
    }
    public function getLockedFilesByUser()
    {
        return $this->service->getLockedFilesByUser();
    }
    public function checkout(FileRequest $request, int $file_id)
    {
        return $this->service->checkout($request, $file_id);
    }
}
