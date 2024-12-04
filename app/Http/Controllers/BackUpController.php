<?php

namespace App\Http\Controllers;

use App\Models\Files;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Repository\Models\BackUpFile\BackUpService;

class BackUpController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new BackUpService();
    }

    public function makeBackUpFile(FileRequest $request, int $id)
    {
        return $this->service->makeBackUpFile($request, $id);
    }

    public function getFile(int $fileID)
    {
        $file = Files::find($fileID);
        $backups = $file->BackUp()->get();

        $backupsWithPaths = $backups->map(function ($backup) {
            return [
                'id' => $backup->id,
                'name' => $backup->name,
                'editor_name' => $backup->editor_name,
                'files_id' => $backup->files_id,
                'created_at' => $backup->created_at,
                'updated_at' => $backup->updated_at,
                'path' => public_path('backUpFile/' . $backup->name)
            ];
        });

        return response()->json([
            'data' => $backupsWithPaths
        ]);
    }
}
