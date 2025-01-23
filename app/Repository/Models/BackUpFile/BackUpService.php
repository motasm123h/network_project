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
    function compareFiles($file1, $file2)
    {
        $file1Contents = file_get_contents($file1);
        $file2Contents = file_get_contents($file2);

        $file1Lines = explode("\n", $file1Contents);
        $file2Lines = explode("\n", $file2Contents);

        $differences = array();


        if (count($file1Lines) > count($file2Lines)) {
            foreach ($file1Lines as $lineNum => $line) {
                if (!in_array($line, $file2Lines)) {
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        } else if (count($file1Lines) < count($file2Lines)) {
            foreach ($file2Lines as $lineNum => $line) {
                if (!in_array($line, $file1Lines)) {
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        'file' => $file1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        } else {
            foreach ($file2Lines as $lineNum => $line) {
                if (!in_array($line, $file1Lines)) {
                    $differences[] = array(
                        'line' => $lineNum + 1,
                        'type' => 'in this line there are change '
                    );
                }
            }
        }

        return $differences;
    }

    public function makeBackUpFile(FileRequest $request, int $id)
    {
        $file = ModelFinder::findOrNull(Files::class, $id);

        $data = $request->validated();
        $fileProcess = app(FileProcess::class);
        $fileCredentials = $fileProcess->filetrait($data);
        $hash = $this->fileService->getHash($fileCredentials['file_path']);
        // dd($fileCredentials);
        $fileType = pathinfo($file['file_name'], PATHINFO_EXTENSION);

        if($fileType == 'txt'){
                if ($file) {
                    // dd($file['file_path'],$fileCredentials['file_path']);
                    $differences = $this->compareFiles($fileCredentials['file_path'], $file['file_path']);
                    if ($differences) {
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

                        dd($differences);
                        // here where i should continune right now 
                        return  $file;
                } else {
                    return $file;
                }
            } 
            else {
                return null;
            }
        }
        

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

               
                return  $file;
            } else {
                return $file;
            }
        } else {
            return null;
        }
    }
}
