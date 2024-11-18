<?php

namespace App\Repository\Models\Files;

use App\Classes\FileServices\FileServices;
use App\Models\Files;
use App\Models\Groups;
use App\Repository\Repo;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\FireBaseServices\FirebaseService;
use App\Http\Requests\FileRequest;
use App\Repository\Models\Interface\Files\AddFile;
use App\Repository\Models\Interface\Files\DeleteFile;


class FilesRepo extends Repo implements AddFile, DeleteFile
{
    use ResponseTrait;
    protected $notificationService;
    private $fileservices;
    public function __construct()
    {
        $this->notificationService = new FirebaseService();
        $this->fileservices = new FileServices();
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
                'locked_by' => null,
                'locked_at' => null,
            ];

            $file = Files::create($data);
            $hash = $this->fileservices->getHash($file->file_path);
            $file['hash'] = $hash;
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


    public function DownloadFile(int $file_id)
    {
        $file = ModelFinder::findOrNull(Files::class, $file_id);

        if (!$file || !isset($file['file_path'])) {
            return $this->apiResponse('File Not Found', null, 404);
        }

        return response()->download($file['file_path']);
    }
}
