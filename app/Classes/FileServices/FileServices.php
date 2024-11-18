<?php

namespace App\Classes\FileServices;

use App\Models\Files;
use App\Repository\Models\Files\GetReport;
use App\Traits\ResponseTrait;

class FileServices
{
    use ResponseTrait;
    private $getReport;
    public function __construct()
    {
        $this->getReport = new GetReport();
    }
    public function generatepdf($logs, int $type)
    {
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

        return $html;
    }

    public function getLogs(int $id, int $type)
    {
        $logs = null;
        if ($type == 1) {
            $logs = $this->getReport->getFileReport($id);
        } else {
            $logs = $this->getReport->getUserReport($id);
        }

        return $logs;
    }

    public function getHash(string $file)
    {
        $hash = md5_file($file);
        return $hash;
    }
}
