<?php

namespace App\Jobs;

use App\Models\Files;
use App\Models\FilesBackUp;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Classes\HelperFunction\FileProcess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateFileProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected $fileId, $fileData;

    public function __construct(int $fileId, array $fileData)
    {
        $this->fileId = $fileId;
        $this->fileData = $fileData;
    }

    public function handle(): void
    {
        $file = Files::find($this->fileId);
        if (!$file) {
            throw new \Exception("File not found");
        }
        // dd($file->file_path);

        if (md5_file($file->file_path) !== md5_file($this->fileData['file_path'])) {
            $fileProcess = app(FileProcess::class);
            $data = $fileProcess->filetraitUpload($file->file_name);
            FilesBackUp::create([
                'files_id' => $file->id,
                'name' => $file->file_name,
                'editor_name' => auth()->user()->name,
            ]);

            $res = $file->update([
                'file_path' => $this->fileData['file_path'],
                'file_name' => $this->fileData['file_name'],
            ]);
            dd($this->fileData['file_path'], "   ", $res, "   ", $file['file_path']);
        } else {
            if (File::exists($this->fileData['file_path'])) {
                File::delete($this->fileData['file_path']);
            }
        }
    }
}
