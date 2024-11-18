<?php

namespace App\Repository\Models\BackUpFile;

use App\Models\Files;
use App\Repository\Repo;
use App\Models\FilesBackUp;
use Illuminate\Http\Request;
use App\Jobs\UpdateFileProcess;

use App\Http\Requests\FileRequest;
use Illuminate\Support\Facades\File;
use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\FileServices\FileServices;

class BackUp extends Repo
{
    private $fileService;
    public function __construct()
    {
        $this->fileService = new FileServices();
        parent::__construct(Files::class);
    }


    public function makeBackUpFile(FileRequest $request, int $id)
    {
        $file = ModelFinder::findOrNull(Files::class, $id);

        $data = $request->validated();
        $fileProcess = app(FileProcess::class);
        $fileCredentials = $fileProcess->filetrait($data);
        $hash = $this->fileService->getHash($fileCredentials['file_path']);
        if ($file) {

            if ($hash != $file['hash']) {
                $data = $fileProcess->filetraitUpload($file->file_name);
                FilesBackUp::create([
                    'files_id' => $file->id,
                    'name' => $file->file_name,
                    'editor_name' => auth()->user()->name,
                ]);

                $res = $file->update([
                    'file_path' => $fileCredentials['file_path'],
                    'file_name' => $fileCredentials['file_name'],
                    'hash' => $hash,
                ]);

                return $this->apiResponse("File updated", $file, 200);
            } else {
                return $this->apiResponse("Files are the same", $file, 201);
            }
        } else {
            return response()->json(['message' => 'File not found'], 299);
        }
    }



    
}
