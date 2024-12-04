<?php

namespace App\Repository\Models\Files;

use App\Models\Files;
use App\Repository\Repo;
use App\Models\file_reservation_logs;
use App\Repository\Models\Interface\Files\GetReport as GR;


class GetReportServices extends Repo implements GR
{

    public function __construct()
    {
        parent::__construct(Files::class);
    }


    public function getReportByColumn(string $column, int $id, string $relation)
    {
        return file_reservation_logs::where($column, $id)
            ->with($relation)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFileReport(int $fileId)
    {
        return $this->getReportByColumn('file_id', $fileId, 'user');
    }

    public function getUserReport(int $userId)
    {
        return $this->getReportByColumn('user_id', $userId, 'file');
    }
}
