<?php

namespace App\Repository\Models\Invitation;

use App\Models\User;
use App\Models\Groups;
use App\Repository\Repo;
use App\Models\Invitation;
use App\Traits\ResponseTrait;
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\InvitationResRequest;

class InvitationRepo extends Repo
{
    use ResponseTrait;
    private $user;
    private $GroupsServices;
    public function __construct($user)
    {
        $this->user = $user;
        // $this->GroupsServices = new GroupsServices($this->user);
        parent::__construct(Invitation::class);
    }

    public function sendInvitation(InvitationRequest $request)
    {
        $validated = $request->validated();
        $groupId = $validated['group_id'];


        $isAdmin = $this->user->isSuperAdminOfGroup($validated['group_id']);

        if (!$isAdmin) {
            return response()->json(['error' => 'You are not authorized to send invitations for this group.'], 403);
        }

        $existingInvitation = Invitation::existingInvitation($validated['group_id'], $validated['receiver_id'], 'pending');

        if ($existingInvitation) {
            return response()->json(['error' => 'This user has already been invited.'], 400);
        }
        parent::create([
            'group_id' => $groupId,
            'sender_id' => $this->user->id,
            'receiver_id' => $validated['receiver_id'],
        ]);

        return response()->json(['success' => 'Invitation sent successfully.']);
    }

    public function respondToInvitation(InvitationResRequest $request, $id)
    {
        $validated = $request->validated();

        $invitation = Invitation::findOrFail($id);

        if ($invitation->receiver_id !== $this->user->id) {
            return response()->json(['error' => 'You are not authorized to respond to this invitation.'], 403);
        }

        $invitation->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            $invitation->group->users()->attach($invitation->receiver_id);
        }

        return response()->json(['success' => 'Invitation response recorded.']);
    }

    public function getUsersNotInGroup($groupId)
    {
        $group = Groups::find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        $usersNotInGroupOrInvited = User::usersNotInGroupOrInvited($groupId);

        $formattedUsers = $usersNotInGroupOrInvited->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        return response()->json($formattedUsers);
    }

    public function receivedInvitations()
    {
        $receivedInvitations = auth()->user()->receivedInvitationFormat('pending');
        return response()->json([
            'success' => 'Invitations received',
            'data' => $receivedInvitations,
        ]);
    }

    public function sentInvitations()
    {
        $sentInvitations = auth()->user()
            ->sentInvitationsFormat('pending');

        return response()->json([
            'success' => 'Invitations sent',
            'data' => $sentInvitations,
        ]);
    }
}