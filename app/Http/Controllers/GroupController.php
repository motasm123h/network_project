<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\Models\Groups\GroupsRepo;

class GroupController extends Controller
{
    private $repo;
    public function __construct()
    {
        $this->repo = new GroupsRepo();
    }

    public function StartGroups(Request $request)
    {
        return $this->repo->StartGroup($request);
    }

    public function LeaveGroups(int $group_id)
    {
        return $this->repo->LeaveGroups($group_id);
    }

    public function JoinGroups(int $group_id)
    {
        return $this->repo->JoinGroups($group_id);
    }

    public function myGroups()
    {
        return $this->repo->myGroups();
    }
}
