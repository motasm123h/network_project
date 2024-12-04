<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Groups;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repository\Models\Groups\GroupsServices;

class GroupController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new GroupsServices(auth()->user());
    }

    public function StartGroups(Request $request)
    {
        return $this->service->StartGroup($request);
    }

    public function LeaveGroups(int $group_id)
    {
        return $this->service->LeaveGroups($group_id);
    }

    public function JoinGroups(int $group_id)
    {
        return $this->service->JoinGroups($group_id);
    }

    public function myGroups()
    {
        return $this->service->myGroups();
    }
    public function getGroups()
    {
        return $this->service->getGroups();
    }
    public function getPoepleGroups(int $group_id)
    {
        return $this->service->getPoepleGroups($group_id);
    }
    public function serachPeople(Request $request, int $group_id)
    {
        return $this->service->serachPeople($request, $group_id);
    }
}
