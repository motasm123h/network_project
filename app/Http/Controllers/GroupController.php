<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Groups;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repository\Models\Groups\GroupsRepo;

class GroupController extends Controller
{
    private $repo;
    public function __construct()
    {
        $this->repo = new GroupsRepo(auth()->user());
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
    public function getGroups()
    {
        return $this->repo->getGroups();
    }
    public function getPoepleGroups(int $group_id)
    {
        return $this->repo->getPoepleGroups($group_id);
    }


    public function sendInvitation(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $groupId = $validated['group_id'];
        $receiverId = $validated['receiver_id'];
        $sender = auth()->user();

        // Check if the sender is the admin of the group
        $isAdmin = DB::table('groups_users')
            ->where('groups_id', $groupId)
            ->where('user_id', $sender->id)
            ->where('is_admin', 1)
            ->exists();

        if (!$isAdmin) {
            return response()->json(['error' => 'You are not authorized to send invitations for this group.'], 403);
        }


        // Check if the user is already invited
        $existingInvitation = Invitation::where('group_id', $groupId)
            ->where('receiver_id', $validated['receiver_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            return response()->json(['error' => 'This user has already been invited.'], 400);
        }

        // Create invitation
        Invitation::create([
            'group_id' => $groupId,
            'sender_id' => $sender->id,
            'receiver_id' => $validated['receiver_id'],
        ]);

        return response()->json(['success' => 'Invitation sent successfully.']);
    }


    public function respondToInvitation(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $invitation = Invitation::findOrFail($id);

        // Ensure only the invited user can respond
        if ($invitation->receiver_id !== auth()->id()) {
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
        // Check if the group exists
        $group = Groups::find($groupId);

        if (!$group) {
            return response()->json(['error' => 'Group not found'], 404);
        }

        // Retrieve all users who are not in the group and don't have a pending invitation
        $usersNotInGroupOrInvited = User::whereDoesntHave('groups', function ($query) use ($groupId) {
            $query->where('groups.id', $groupId); // Exclude users in the group
        })->whereDoesntHave('invitations', function ($query) use ($groupId) {
            $query->where('group_id', $groupId); // Exclude users with pending invitations
        })->get();

        // Optional: Format the response
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
        $receivedInvitations = auth()->user()
            ->receivedInvitations()
            ->where('status', 'pending')
            ->with(['group:id,name', 'sender:id,name,email']) // Load group and sender details
            ->get();

        return response()->json([
            'success' => 'Invitations received',
            'data' => $receivedInvitations,
        ]);
    }

    public function sentInvitations()
    {
        $sentInvitations = auth()->user()
            ->sentInvitations()
            ->where('status', 'pending')
            ->with(['group:id,name', 'receiver:id,name,email']) // Load group and receiver details
            ->get();

        return response()->json([
            'success' => 'Invitations sent',
            'data' => $sentInvitations,
        ]);
    }
    
    public function deleteInvitations(int $invitationID)
    {
        $Invitation = Invitation::findOrFail($invitationID);
        return response()->json([
            'success' => 'Invitations Delete',
            'data' => $Invitation->delete(),
        ]);
    }
}
