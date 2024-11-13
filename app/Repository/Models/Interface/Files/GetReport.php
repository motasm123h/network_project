<?php

namespace App\Repository\Models\Interface\Files;

use Illuminate\Http\Request;

interface GetReport
{
    public function getFileReport(int $file_id);
    public function getUserReport(int $file_id);
}
