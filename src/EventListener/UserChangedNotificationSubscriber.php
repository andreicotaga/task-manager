<?php

namespace App\EventListener;

use App\Entity\Activity;
use App\Event\NotificationsReadEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserChangedNotificationSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            NotificationsReadEvent::NAME => 'onReadAction'
        ];
    }

    public function onReadAction(NotificationsReadEvent $event)
    {
        $notificationsIds = array_column($event->getNotifications(), 'id');

        if (!empty($notificationsIds)) {
            $this->entityManager
                ->getRepository(Activity::class)
                ->updateUnreadNotifications($notificationsIds);
        }
    }
}