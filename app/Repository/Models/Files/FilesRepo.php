<?php

namespace App\Repository\Models\Files;

use App\Models\Files;
use App\Models\Groups;
use App\Repository\Repo;
// use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
// use App\Models\file_reservation_logs;
// use App\Http\Controllers\NotiController;
use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\FireBaseServices\FirebaseService;
use App\Http\Requests\FileRequest;
use App\Repository\Models\Interface\Files\AddFile;
use App\Repository\Models\Interface\Files\DeleteFile;
// use App\Repository\Models\Interface\Files\LockUnLockFile;
// use App\Repository\Models\Notification\NotificationService;

class FilesRepo extends Repo implements AddFile, DeleteFile
{
    use ResponseTrait;
    protected $notificationService;
    public function __construct()
    {
        $this->notificationService = new FirebaseService();
        parent::__construct(Files::class);
    }

    public function addFile(FileRequest $request, int $group_id)
    {
        $data = $request->validated(); 
        try {
            $fileProcess = app(FileProcess::class);
            $fileCredentials = $fileProcess->filetrait($data);

            $data = [
                'file_path' => $fileCredentials['file_path'],
                'file_name' => $fileCredentials['file_name'],
                'group_id' => $group_id,
            ];

            $file = parent::create($data);

            return $this->apiResponse('File added successfully', $file, 200);
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), null, 400);
        }
    }

    public function deleteFile(int $file_id)
    {
        $file = ModelFinder::findOrNull(Files::class, $file_id);
        if ($file) {
            parent::delete($file_id);
            return $this->apiResponse('File deleted successfully', null, 200);
        }
        return $this->apiResponse("File not found", null, 404);
    }

    public function getFiles(int $group_id)
    {
        $group = ModelFinder::findOrNull(Groups::class, $group_id);
        if ($group) {
            $files = DB::table('files')->where('group_id', $group_id)->get();
            return $this->apiResponse('Files for this Group', $files, 200);
        }
        return $this->apiResponse('Files Not Found', null, 200);
    }
}
