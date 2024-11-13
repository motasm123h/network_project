<?php

namespace App\Repository\Models\Interface\Groups;

interface CreateGroup
{
    public function CreateGroup(string $name);
    public function AddOwnerToGroup(int $id_group,int $id_user);
}
