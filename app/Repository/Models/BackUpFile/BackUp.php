<?php

namespace App\Repository\Models\BackUpFile;

use App\Models\Files;
use App\Repository\Repo;
use Illuminate\Http\Request;
use App\Jobs\UpdateFileProcess;

use App\Classes\HelperFunction\FileProcess;
use App\Classes\HelperFunction\ModelFinder;
use App\Http\Requests\FileRequest;

class BackUp extends Repo
{

    public function __construct()
    {
        parent::__construct(Files::class);
    }


    public function makeBackUpFile(FileRequest $request, int $id, int $group_id)
    {
        $file = ModelFinder::findOrNull(Files::class, $id);

        $data = $request->validated();

        // $request->validate([
        //     'file' => 'required|file',
        // ]);

        if ($file) {
            $fileProcess = app(FileProcess::class);

            $fileCredentials = $fileProcess->filetrait($data);
            UpdateFileProcess::dispatch($file->id, $fileCredentials);

            return response()->json(['message' => 'Backup process initiated']);
        }

        return response()->json(['message' => 'File not found'], 404);
    }
}
