<?php

namespace App\Repository\Models\Files;

use League\Csv\Writer;
use App\Repository\Repo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use App\Repository\Models\Files\GetReport;
use App\Repository\Models\Interface\Files\FileExport as FE;

class FileExport extends Repo implements FE
{
    private $getReport;
    public function __construct()
    {
        $this->getReport = new GetReport();
    }

    public function exportFileReportToPdf(int $id, int $type)
    {
        $logs = null;
        if ($type == 1) {
            $logs = $this->getReport->getFileReport($id);
        } else {
            $logs = $this->getReport->getUserReport($id);
        }

        $html = '<h1>File Lock Report</h1>';
        if ($type == 1) {
            $html .= '<h1>Report By File</h1>';
        } else {
            $html .= '<h1>Report By User</h1>';
        }
        $html .= '<table>';
        $html .= '<tr><th>User</th><th>Action</th><th>FileName</th><th>Timestamp</th></tr>';

        foreach ($logs as $log) {
            $html .= '<tr>';
            $html .= "<td>{$log->user->name}</td>";
            $html .= "<td>{$log->action}</td>";
            $html .= "<td>{$log->file->file_name}</td>";
            $html .= "<td>{$log->created_at}</td>";
            $html .= '</tr>';
        }

        $html .= '</table>';

        $filePath = storage_path("app/public/reports/file_report_{$id}.pdf");

        $directoryPath = storage_path('app/public/reports');
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        $pdf = Pdf::loadHTML($html);
        $pdf->save($filePath);

        return response()->json(
            [
                'file_url' => asset("storage/reports/file_report_{$id}.pdf")
            ],
            200
        );
    }

    public function exportFileReportToCsv(int $id, int $type)
    {
        $logs = null;
        if ($type == 1) {
            $logs = $this->getReport->getFileReport($id);
        } else {
            $logs = $this->getReport->getUserReport($id);
        }
        $filePath = storage_path("app/public/reports/file_report_{$id}.csv");

        $directoryPath = storage_path('app/public/reports');
        if (!File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

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

        return response()->json(
            [
                'file_url' => asset("storage/reports/file_report_{$id}.csv")
            ],
            200
        );
    }
}
