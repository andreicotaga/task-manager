<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Event\NotificationsReadEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @Route("/notifications", name="app_notifications_index")
     */
    public function index()
    {
        $countNotifications = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->getCountUnreadActivitiesByUserId($this->getUser()->getId());

        $notifications = $this->getDoctrine()
            ->getRepository(Activity::class)
            ->getUnreadActivitiesByUserId($this->getUser()->getId());

        $event = new NotificationsReadEvent($notifications);
        $this->eventDispatcher->dispatch($event, NotificationsReadEvent::NAME);

        return $this->render('notifications.html.twig', [
            'username' => $this->getUser()->getUsername(),
            'notifications' => $notifications,
            'countNotifications' => $countNotifications
        ]);
    }
}