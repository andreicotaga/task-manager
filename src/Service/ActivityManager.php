<?php

namespace App\Service;

use App\Entity\Activity;
use Doctrine\ORM\EntityManagerInterface;

class ActivityManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Log an activity
     *
     * @param $userId
     * @param $taskId
     * @param $action
     * @param $field
     * @param $oldValue
     * @param $newValue
     */
    public function logActivity($userId, $taskId, $action, $field, $oldValue, $newValue, $status)
    {
        $this->entityManager
            ->getRepository(Activity::class)
            ->createActivity($userId, $taskId, $action, $field, $oldValue, $newValue, $status);
    }

    /**
     * @param $userId
     * @param $taskId
     */
    public function getActivities($userId, $taskId)
    {
        return $this->entityManager
            ->getRepository(Activity::class)
            ->getActivities($userId, $taskId);
    }
}