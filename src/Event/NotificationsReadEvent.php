<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class NotificationsReadEvent extends Event
{
    public const NAME = 'notifications.read';

    protected $notifications;

    public function __construct($notifications)
    {
        $this->notifications = $notifications;
    }

    public function getNotifications()
    {
        return $this->notifications;
    }
}