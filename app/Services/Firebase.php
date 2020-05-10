<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class Firebase {
    const TOPIC_NEWS = 'news';

    public static function sendMessaging($title = 'title', $body = '', $topic = self::TOPIC_NEWS)
    {
        $messaging = self::factory()->createMessaging();

        $data = [];
        $imageUrl = 'http://poskota.tv/img/logo/logo.png';

        $notification = Notification::create($title, $body)
            ->withImageUrl($imageUrl);

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification);
        if (count($data))
            $message->withData($data);

        $messaging->send($message);
    }

    public static function subsMessaging(string $registrationToken, string $topic)
    {
        $messaging = self::factory()->createMessaging();

        $registrationTokens = [
            $registrationToken,
        ];

        try {
            $messaging->subscribeToTopic($topic, $registrationTokens);

            $appInstance = $messaging->getAppInstance($registrationToken);
            $instanceInfo = $appInstance->rawData();
        } catch (Exception $e) {
            report($e);
            return [false, $e->getMessage()];
        }

        return [ true, $instanceInfo ];
    }

    public static function authUser($id)
    {
        $auth = self::factory()->createAuth();

        try {
            $user = $auth->getUser($id);
        } catch (Exception $e) {
            if ($e instanceof UserNotFound) {
                $message = 'User from social is invalid';
            } else {
                report($e);
                $message = 'Social user: '.$e->getMessage();
            }

            return [ false, $message ];
        }

        return [ true, $user ];
    }

    private static function factory()
    {
        // 731577502fde35446c9c7708f1fa82ceb04f51a7
        $factory = (new Factory)
            ->withServiceAccount(storage_path('poskota-app-731577502fde.json'))
            ->withDisabledAutoDiscovery();
        return $factory;
    }
}
