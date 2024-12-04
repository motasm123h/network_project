<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Http\Requests\InvitationResRequest;
use App\Repository\Models\Invitation\InvitationService;

class InvitationController extends Controller
{
    private $service;
    public function __construct()
    {
        $this->service = new InvitationService(auth()->user());
    }
    public function sendInvitation(InvitationRequest $request)
    {
        return $this->service->sendInvitation($request);
    }


    public function respondToInvitation(InvitationResRequest $request, $id)
    {
        return $this->service->respondToInvitation($request, $id);
    }


    public function getUsersNotInGroup($groupId)
    {
        return $this->service->getUsersNotInGroup($groupId);
    }

    public function receivedInvitations()
    {
        return $this->service->receivedInvitations();
    }

    public function sentInvitations()
    {
        return $this->service->sentInvitations();
    }

    public function deleteInvitations(int $invitationID)
    {
        return $this->service->deleteInvitations($invitationID);
    }
}
