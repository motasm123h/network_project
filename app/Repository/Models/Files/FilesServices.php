<?php

namespace App\Repository\Models\Files;

use App\Models\Files;
use App\Models\Groups;
use App\Repository\Repo;
use App\Models\FilesBackUp;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Requests\FileRequest;
use Illuminate\Support\Facades\DB;
use App\Classes\FileServices\FileServices;
use App\Classes\HelperFunction\FileProcess;
// use App\Repository\Models\Files\FilesServices;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\FireBaseServices\FirebaseService;
use App\Repository\Models\Interface\Files\AddFile;
use App\Repository\Models\Interface\Files\DeleteFile;

class FilesServices extends Repo implements AddFile, DeleteFile
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
            if (auth()->user()->isSuperAdminOfGroup($group_id)) {
                $data['status'] = 'approved';
            } else {
                $data['status'] = 'pending';
            }

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

        $files = $this->fileservices->getFile($group_id, ['pending', 'approved']);
        if ($files) {
            return $this->apiResponse('Files for this Group', $files, 200);
        }

        return $this->apiResponse('Files Not Found', null, 200);
        return $this->apiResponse('Files Not Found', null, 200);
    }

    public function getFilesForCheck(int $group_id)
    {
        $files = $this->fileservices->getFile($group_id, ['pending']);
        if ($files) {
            return $this->apiResponse('Files for this Group', $files, 200);
        }

        return $this->apiResponse('Files Not Found', null, 200);
    }


    public function DownloadFile(int $file_id)
    {
        $file = ModelFinder::findOrNull(Files::class, $file_id);
        // // $fileb = ModelFinder::findOrNull(FilesBackUp::class, $file_id);
        // if (!$file || !isset($file['path'])) {
        //     return $this->apiResponse('File Not Found', null, 404);
        // } else {

        //     return response()->download($file['file_path']);
        // }
        if (!$file || !isset($file['file_path'])) {
            return $this->apiResponse('File Not Found', null, 404);
        }

        return response()->download($file['file_path']);
    }

    public function fileRespond(Request $request, int $file_id)
    {
        $file = ModelFinder::findOrNull(Files::class, $file_id);
        $atter = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);
        if (!$file || !isset($file['file_path'])) {
            return $this->apiResponse('File Not Found', null, 404);
        }
        $file->update([
            'status' => $atter['status'],
        ]);
        return $this->apiResponse('File Edit', $file, 200);
    }
}
