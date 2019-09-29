<?php

namespace NotificationChannels\Routee;

use NotificationChannels\Routee\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;

class RouteeChannel
{
    protected $api;

    public function __construct(RouteeApi $api)
    {
        $this->api = $api;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Routee\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {

        $to = $notifiable->routeNotificationFor('routee');
        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toRoutee($notifiable);
        if (\is_string($message)) {
            $message = new RouteeMessage($message);
        }

        return $this->api->sendSMS($to, $message->content);
    }
}
