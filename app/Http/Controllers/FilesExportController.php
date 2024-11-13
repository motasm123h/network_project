<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Models\Files\FileExport;

class FilesExportController extends Controller
{
    private $repoExportFile;
    public function __construct()
    {
        return [
            $this->repoExportFile = new FileExport(),
        ];
    }

    public function exportFileReportToPdf(int $id, int $type)
    {
        return $this->repoExportFile->exportFileReportToPdf($id, $type);
    }

    public function exportFileReportToCsv(int $id, int $type)
    {
        return $this->repoExportFile->exportFileReportToCsv($id, $type);
    }
}
