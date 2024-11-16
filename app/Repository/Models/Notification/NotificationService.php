<?php

namespace App\Repository\Models\Notification;

use Illuminate\Http\Request;
use App\Classes\FireBaseServices\FirebaseService;

class NotificationService
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function send(array $request, string $token = 'exR4rljOm360Rd5AzgpTfz:APA91bFd6SiGxBgZfF0ntLSlmHA-smB7P_lqdNWAfcx9eA8NqvVTdR0SI_eBa-9DyyGMDoJlEgWDi8lsx7GnxXdhs5ZJ_GzJ8R2T8HW3MUGl6qzxbxRICnE')
    {
        $attributes = [
            'name' => $request['name'],
            'message' => $request['message'],
        ];

        $token = auth()->user()->FCT;
        $result = $this->firebaseService->sendNotificationTo($token, $attributes['name'], $attributes['message']);
        if ($result['success']) {
            return ['status' => 'success', 'message' => 'Notification sent successfully', 'res' => $result];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to send notification',
                'details' => $result['message'] ?? 'Unknown error',
                'failedTokens' => $result['failedTokens'] ?? []
            ];
        }
    }

    public function saveFCT(Request $request)
    {
        $atter = $request->validate([
            'FCT' => ['required'],
        ]);

        $user = auth()->user();
        $user->update([
            'FCT' => $atter['FCT'],
        ]);

        return [
            'message' => true,
        ];
    }
}
