<?php

namespace App\Repository\Models\Groups;

use App\Models\Groups;
use App\Repository\Repo;
use App\Models\Invitation;
// use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use App\Classes\HelperFunction\ModelFinder;
use App\Classes\GroupsServices\GroupsServices as GS;
use App\Repository\Models\Interface\Groups\JoinGroup;
use App\Repository\Models\Interface\Groups\LeaveGroup;
use App\Repository\Models\Interface\Groups\CreateGroup;

class GroupsServices extends Repo implements LeaveGroup, JoinGroup, CreateGroup
{
    use ResponseTrait;
    private $user;
    private $GroupsServices;
    public function __construct($user)
    {
        $this->user = $user;
        $this->GroupsServices = new GS($this->user);
        parent::__construct(Groups::class);
    }

    public function LeaveGroups(int $id)
    {
        $group = ModelFinder::findOrNull(Groups::class, $id);

        if (is_null($group)) {
            return $this->apiResponse('Group not found', statuscode: 404);
        }
        $res = $this->GroupsServices->groupsCheck($group, 2, $this->user->id);
        return $this->apiResponse($res['message'], statuscode: $res['code']);
    }

    public function JoinGroups(int $id)
    {
        $group = ModelFinder::findOrNull(Groups::class, $id);

        if (is_null($group)) {
            return $this->apiResponse('Group not found', statuscode: 404);
        }
        $res = $this->GroupsServices->groupsCheck($group, 1, $this->user->id);
        // if($res['code'] == 200){

        // }
        return $this->apiResponse($res['message'], statuscode: $res['code']);
    }


    public function StartGroup($request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        $id_group = $this->CreateGroup($data['name']);

        if (!is_null($id_group)) {
            $data = $this->AddOwnerToGroup($id_group, $this->user->id);
            return $this->apiResponse('Group created successfully', [
                'group_id' => $data['group']['id'],
                'name' => $data['group']['name']
            ], 200);
        }

        return $this->apiResponse('Group creation failed', null, 500);
    }

    public function CreateGroup(string $name)
    {
        $group = Groups::create(['name' => $name]);
        return $group ? $group->id : null;
    }

    public function AddOwnerToGroup(int $groupId, int $user)
    {
        $group = ModelFinder::findOrNull(Groups::class, $groupId);

        if ($group) {
            $group->users()->attach($user, ['is_admin' => 1]);
            return [
                'group' => $group,
                'status' => 200,
            ];
        }
        return [
            'group' => null,
            'status' => 404,
        ];;
    }

    public function myGroups()
    {
        $groups = $this->GroupsServices->getMyGroups();
        return $this->apiResponse('My Groups ', $groups, 200);
    }

    public function getGroups()
    {
        $groups = $this->GroupsServices->getAvailableGroups();
        return $this->apiResponse('Groups not joined by user', $groups, 200);
    }
    public function getPoepleGroups(int $group_id)
    {
        $group = ModelFinder::findOrNull(Groups::class, $group_id);
        return $this->apiResponse('My People ', $group->users()->get()->makeHidden('pivot'), 200);
    }
    public function serachPeople($request, $group_id)
    {
        $group = Groups::where('id', $group_id)->first();
        $validatedData = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $searchTerm = $validatedData['content'];
        $data = [];
        $result = DB::table('users')
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orWhere('email', 'like', '%' . $searchTerm . '%')
            ->get();

        foreach ($result as $res) {
            $existingInvitation = Invitation::existingInvitation($group_id, $res->id, ['pending', 'accepted', 'rejected']);
            if ($group->users()->where('user_id', $res->id)->exists() || !is_null($existingInvitation)) {
                continue;
            } else {
                $data[] = $res;
            }
        }
        return $this->apiResponse('Search results', $data, 200);
    }
}
