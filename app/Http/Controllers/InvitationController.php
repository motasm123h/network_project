<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\InvitationResRequest;
use App\Repository\Models\Invitation\InvitationRepo;

class InvitationController extends Controller
{
    private $repo;
    public function __construct()
    {
        $this->repo = new InvitationRepo(auth()->user());
    }
    public function sendInvitation(InvitationRequest $request)
    {
        return $this->repo->sendInvitation($request);
    }


    public function respondToInvitation(InvitationResRequest $request, $id)
    {
        return $this->repo->respondToInvitation($request, $id);
    }


    public function getUsersNotInGroup($groupId)
    {
        return $this->repo->getUsersNotInGroup($groupId);
    }

    public function receivedInvitations()
    {
        return $this->repo->receivedInvitations();
    }

    public function sentInvitations()
    {
        return $this->repo->sentInvitations();
    }

    // public function deleteInvitations(int $invitationID)
    // {
    //     $Invitation = Invitation::findOrFail($invitationID);
    //     return response()->json([
    //         'success' => 'Invitations Delete',
    //         'data' => $Invitation->delete(),
    //     ]);
    // }
}
