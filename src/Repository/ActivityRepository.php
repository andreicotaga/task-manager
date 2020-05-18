<?php

namespace App\Repository;

use App\Constants\ActivityActions;
use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function createActivity($userId, $taskId, $action, $field, $oldValue, $newValue, $status)
    {
        $entityManager = $this->getEntityManager();

        $activity = new Activity();
        $activity->setUserId($userId);
        $activity->setTaskId($taskId);
        $activity->setAction($action);
        $activity->setField($field);
        $activity->setOldValue($oldValue);
        $activity->setNewValue($newValue);
        $activity->setStatus($status);

        $entityManager->persist($activity);
        $entityManager->flush();
    }

    public function getActivities($userId, $taskId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('a.new_value', 'a.old_value', 'a.action', 'a.field', 'a.created_at', 'u.email')
            ->from('App\Entity\Activity', 'a')
            ->where('a.user_id = :user_id')
            ->andWhere('a.task_id = :task_id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'a.user_id = u.id')
            ->orderBy('a.created_at', 'DESC')
            ->setParameter('user_id', $userId)
            ->setParameter('task_id', $taskId)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getCountUnreadActivitiesByUserId($userId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('count(a.id)')
            ->from('App\Entity\Activity', 'a')
            ->where('a.user_id = :user_id')
            ->andWhere('a.status = :status')
            ->orderBy('a.created_at', 'DESC')
            ->setParameter('user_id', $userId)
            ->setParameter('status', ActivityActions::UNREAD_STATUS)
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getUnreadActivitiesByUserId($userId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('a.id', 'a.new_value', 'a.old_value', 'a.action', 'a.field', 'a.created_at', 'u.email')
            ->from('App\Entity\Activity', 'a')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'a.user_id = u.id')
            ->where('a.user_id = :user_id')
            ->andWhere('a.status = :status')
            ->orderBy('a.created_at', 'DESC')
            ->setParameter('user_id', $userId)
            ->setParameter('status', ActivityActions::UNREAD_STATUS)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function updateUnreadNotifications($notificationIds)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        $q = $qb->update('App\Entity\Activity', 'a')
            ->set('a.status', ':status')
            ->add('where', $qb->expr()->in('a.id', $notificationIds))
            ->setParameter('status', ActivityActions::READ_STATUS)
            ->getQuery();

        return $q->execute();
    }

    // /**
    //  * @return Activity[] Returns an array of Activity objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Activity
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
