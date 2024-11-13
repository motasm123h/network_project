<?php

namespace App\Repository\Models\Groups;

use App\Models\Groups;
use App\Repository\Repo;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Classes\HelperFunction\ModelFinder;
use App\Repository\Models\Interface\Groups\JoinGroup;
use App\Repository\Models\Interface\Groups\LeaveGroup;
use App\Repository\Models\Interface\Groups\CreateGroup;

class GroupsRepo extends Repo implements LeaveGroup, JoinGroup, CreateGroup
{
    use ResponseTrait;

    public function __construct()
    {
        parent::__construct(Groups::class);
    }

    public function LeaveGroups(int $id)
    {

        $group = ModelFinder::findOrNull(Groups::class, $id);
        if ($group) {
            if ($group->users()->where('user_id', auth()->user()->id)->exists()) {
                $group->users()->detach(auth()->user()->id);
                return $this->apiResponse('User left the group successfully', null, 200);
            } else {
                return $this->apiResponse('User is not in the group', null, 409);
            }
        }
        return $this->apiResponse('Group not found', null, 404);
    }

    public function JoinGroups(int $id)
    {
        $group = ModelFinder::findOrNull(Groups::class, $id);

        if ($group) {
            if (!$group->users()->where('user_id', auth()->user()->id)->exists()) {
                $group->users()->attach(auth()->user()->id);
                return $this->apiResponse('User joined the group successfully', null, 200);
            } else {
                return $this->apiResponse('User is already in the group', null, 409);
            }
        }
        return $this->apiResponse('Group not found', null, 404);
    }


    public function StartGroup($request)
    {
        $data = $request->validate([
            'name' => 'required'
        ]);

        $id_group = $this->CreateGroup($data['name']);

        if (!is_null($id_group)) {
            $data = $this->AddOwnerToGroup($id_group, auth()->user()->id);
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

    public function AddOwnerToGroup(int $groupId, int $userId)
    {
        $group = Groups::find($groupId);

        if ($group) {
            $group->users()->attach($userId, ['is_admin' => 1]);
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
        $groups = auth()->user()->groups()->get()->makeHidden('pivot');
        return $this->apiResponse('My Groups ', $groups, 200);
    }
}
