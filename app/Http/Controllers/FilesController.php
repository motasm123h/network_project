<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Repository\Models\Files\FilesServices;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    private $service;
    public function __construct()
    {
        return [
            $this->service = new FilesServices(),
        ];
    }

    public function addFile(FileRequest $request, int $group_id)
    {
        return $this->service->addFile($request, $group_id);
    }

    public function deleteFile(int $file_id)
    {
        return $this->service->deleteFile($file_id);
    }

    public function getFiles(int $group_id)
    {
        return $this->service->getFiles($group_id);
    }
    public function getFilesForCheck(int $group_id)
    {
        return $this->service->getFilesForCheck($group_id);
    }

    public function DownloadFile(int $file_id)
    {
        return $this->service->DownloadFile($file_id);
    }
    public function fileRespond(Request $request, int $file_id)
    {
        return $this->service->fileRespond($request, $file_id);
    }
}
