<?php

namespace App\Helper;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseHelper
{
    public function sendNotification($token, $title, $body, $data = [])
    {
        $messaging = Firebase::messaging();

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        try {
            $messaging->send($message);
            return true;
        } catch (\Exception $e) {
            \Log::error('FCM Notification Error: ' . $e->getMessage());
            return false;
        }
    }
}
