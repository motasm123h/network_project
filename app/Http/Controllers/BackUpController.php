<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use App\Repository\Models\BackUpFile\BackUp;

class BackUpController extends Controller
{
    private $repo;
    public function __construct()
    {
        $this->repo = new BackUp();
    }

    public function makeBackUpFile(FileRequest $request, int $id, int $group_id)
    {
        return $this->repo->makeBackUpFile($request, $id, $group_id);
    }
}
