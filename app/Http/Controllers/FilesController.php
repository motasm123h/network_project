<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Repository\Models\Files\FilesRepo;
use App\Repository\Models\Files\FileExport;

class FilesController extends Controller
{
    private $repo;
    public function __construct()
    {
        return [
            $this->repo = new FilesRepo(),
        ];
    }

    public function addFile(FileRequest $request, int $group_id)
    {
        return $this->repo->addFile($request, $group_id);
    }

    public function deleteFile(int $file_id)
    {
        return $this->repo->deleteFile($file_id);
    }

    public function getFiles(int $group_id)
    {
        return $this->repo->getFiles($group_id);
    }
}
