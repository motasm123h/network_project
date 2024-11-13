<?php

namespace App\Repository\Models\Files;

use App\Models\Files;
use App\Repository\Repo;
use App\Models\file_reservation_logs;
use App\Repository\Models\Interface\Files\GetReport as GR;


class GetReport extends Repo implements GR
{

    public function __construct()
    {
        parent::__construct(Files::class);
    }

    public function getFileReport(int $fileId)
    {
        return file_reservation_logs::where('file_id', $fileId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserReport(int $userId)
    {
        return file_reservation_logs::where('user_id', $userId)
            ->with('file')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
