<?php

namespace App\Classes\FireBaseServices;

use App\Models\Files;
use App\Models\Groups;
use App\Repository\Models\Notification\NotificationService;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $service;

    public function __construct()
    {
        $this->service = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->createMessaging();
    }

    public function sendNotificationTo(string $token, string $title, string $body)
    {
        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::new()->withNotification($notification);

            $report = $this->service->sendMulticast($message, $token);

            if (count($report->successes()) > 0) {
                return [
                    'success' => true,
                    'failedTokens' => $report->failures(),
                ];
            }

            $failedTokens = [];
            foreach ($report->failures() as $failure) {
                $failedTokens[] = [
                    'token' => $failure->target()->value(),
                    'error' => $failure->error()->getMessage(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send notifications',
                'failedTokens' => $failedTokens,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getUserToNotifi(int $file_id)
    {
        $group = Files::where('id', $file_id)->select('group_id')->first();
        $users = Groups::where('id', $group->group_id)->first();
        $data = $users->users()->select('users.id', 'users.name')->get()->makeHidden('pivot');
        return $data;
    }

    public function sendNotifiToUser($data, string $message)
    {
        // $group = Files::where('id', $file_id)->select('group_id')->first();
        // $users = Groups::where('id', $group->group_id)->first();
        // $data = $users->users()->select('users.id', 'users.name')->get()->makeHidden('pivot');
        $otherController = new NotificationService($this);
        $atter = [
            "message" => $message,
        ];
        foreach ($data as $d) {
            $atter['name'] = $d['name'];
            $otherController->send($atter);
        }
    }
}
