<?php

namespace App\Repository\Models\Files;

use League\Csv\Writer;
use App\Repository\Repo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use App\Classes\FileServices\FileServices;
use App\Repository\Models\Interface\Files\FileExport as FE;

class FileExport extends Repo implements FE
{
    private $FileServices;
    public function __construct()
    {
        $this->FileServices = new FileServices();
    }

    public function exportFileReportToPdf(int $id, int $type)
    {
        $logs = $this->FileServices->getLogs($id, $type);
        $html = $this->FileServices->generatepdf($logs, $type);

        if (empty($html)) {
            return response()->json(['error' => 'HTML content is empty'], 500);
        }

        $fileName = "file_report_{$id}.pdf";
        $filePath = storage_path("app/public/reports/{$fileName}");
        $directoryPath = storage_path('app/public/reports');

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        try {
            $pdf = Pdf::loadHTML($html);
            $pdf->save($filePath);

            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate or download PDF'], 500);
        }
    }

    public function exportFileReportToCsv(int $id, int $type)
    {
        $logs = $this->FileServices->getLogs($id, $type);

        $filePath = storage_path("app/public/reports/file_report_{$id}.csv");
        $directoryPath = storage_path('app/public/reports');

        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        try {
            $csv = Writer::createFromPath($filePath, 'w+');
            $csv->insertOne(['User', 'Action', 'FileName', 'Timestamp']);

            foreach ($logs as $log) {
                $csv->insertOne([
                    $log->user->name,
                    $log->action,
                    $log->file->file_name,
                    $log->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate or download CSV', 'message' => $e->getMessage()], 500);
        }
    }
}
