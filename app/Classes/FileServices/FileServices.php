<?php

namespace App\Classes\FileServices;

use App\Models\Files;
use App\Models\Groups;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Classes\HelperFunction\ModelFinder;
use App\Repository\Models\Files\GetReportServices;

class FileServices
{
    use ResponseTrait;
    private $getReport;
    public function __construct()
    {
        $this->getReport = new GetReportServices();
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

    public function getFile(int $group_id, $status)
    {
        $group = ModelFinder::findOrNull(Groups::class, $group_id);

        if ($group) {
            $files = DB::table('files')
                ->where('group_id', $group_id)
                ->whereIn('status', $status)
                ->get();

            return $files;
        }

        return null;
    }
}
