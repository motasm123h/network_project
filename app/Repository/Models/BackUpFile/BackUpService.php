<?php

namespace App\Repository\Models\BackUpFile;

use App\Models\Files;
use App\Repository\Repo;
use App\Models\FilesBackUp;
use App\Http\Requests\FileRequest;
use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\FileServices\FileServices;

class BackUpService extends Repo
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

                // return $this->apiResponse("File updated", $file, 200);
                // dd($file);
                return  $file;
            } else {
                return $file;
            }
        } else {
            return null;
        }
    }
}
