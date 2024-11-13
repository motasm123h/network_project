<?php

namespace App\Repository\Models\Interface\Files;

use Illuminate\Http\Request;

interface FileExport
{
    public function exportFileReportToPdf(int $id, int $type);
    public function exportFileReportToCsv(int $id, int $type);
}
