<?php

namespace App\Classes\GroupsServices;

use App\Models\User;
use App\Models\Groups;
use Illuminate\Support\Arr;
use App\Traits\ResponseTrait;

class GroupsServices
{
    private $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    use ResponseTrait;
    private $actions_type = ['join' => '1', 'leave' => '2'];
    public function groupsCheck(Groups $group, int $type, int $userId)
    {
        if ($type != $this->actions_type['join']) {
            if (!$group->users()->where('user_id', $userId)->exists()) {
                return ['message' => 'User is not in the group', 'code' => 409];
            }

            $superAdmin = $group->superAdmin()->first();
            if ($superAdmin && $superAdmin->id == $userId) {
                $group->delete();
                return ['message' => 'User left the group successfully and the group was deleted', 'code' => 200];
            }

            $group->users()->detach($userId);
            return ['message' => 'User left the group successfully', 'code' => 200];
        }

        if ($group->users()->where('user_id', $userId)->exists()) {
            return ['message' => 'User is already in the group', 'code' => 409];
        }

        $group->users()->attach($userId);
        return ['message' => 'User joined the group successfully', 'code' => 200];
    }



    private function getUserGroups()
    {
        $res = [];
        $data = $this->user->groups()->get();
        foreach ($data as $da) {
            $da['is_admin'] = $da['pivot']['is_admin'];
            $da->makeHidden('pivot');
            $res[] = $da;
        }
        return $res;
    }

    public function getMyGroups()
    {
        return $this->getUserGroups();
    }

    public function getAvailableGroups()
    {
        $userGroupIds = Arr::pluck($this->getUserGroups(), 'id');

        return Groups::whereNotIn('id', $userGroupIds)->get();
    }


    public function isAdmmin() {}
}
