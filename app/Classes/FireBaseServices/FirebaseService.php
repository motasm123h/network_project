<?php

namespace App\Classes\FireBaseServices;

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
}
