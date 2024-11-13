<?php

namespace App\Classes\HelperFunction;


class FileProcess
{
    public function filetrait(array $file)
    {
        $fileType = $file['file']->getMimeType();
        $fileName = time() . '-' . auth()->user()->name . '.' . $file['file']->extension();

        switch ($fileType) {
            case 'application/pdf':
                $filePath = $file['file']->move(public_path('pdf'), $fileName);
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $filePath = $file['file']->move(public_path('docx'), $fileName);
                break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                $filePath = $file['file']->move(public_path('pptx'), $fileName);
                break;
            default:
                throw new \Exception("Unsupported file type");
        }

        return [
            'file_path' => $filePath->getRealPath(),
            'file_name' => $fileName,
        ];
    }
    public function filetraitUpload(string $fileName)
    {
        if (empty($fileName)) {
            throw new \Exception("File name is empty");
        }

        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        switch ($fileType) {
            case 'pdf':
                $sourcePath = public_path('pdf/' . $fileName);
                // dd($sourcePath);
                break;
            case 'docx':
                $sourcePath = public_path('docx/' . $fileName);
                break;
            case 'pptx':
                $sourcePath = public_path('pptx/' . $fileName);
                break;
            default:
                throw new \Exception("Unsupported file type: $fileType");
        }

        $destinationPath = public_path('backUpFile/' . $fileName);

        if (!is_dir(public_path('backUpFile'))) {
            mkdir(public_path('backUpFile'), 0755, true);
        }

        if (file_exists($sourcePath)) {
            rename($sourcePath, $destinationPath);
        } else {
            dd($destinationPath);
            throw new \Exception("File does not exist at $sourcePath");
        }

        return [
            'file_name' => $fileName,
            'file_path' => $destinationPath
        ];
    }
}
