<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findTasksByUserId($userId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('t.id', 't.title', 'ts.name as status', 't.created_at', 'u.email')
            ->from('App\Entity\Task', 't')
            ->leftJoin('App\Entity\UserTask', 'ut', 'WITH', 'ut.task_id = t.id')
            ->leftJoin('App\Entity\TaskStatus', 'ts', 'WITH', 't.status = ts.id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'ut.user_id = u.id')
            ->where('ut.user_id = :user_id')
            ->orderBy('t.status', 'ASC')
            ->setParameter('user_id', $userId)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function findTasksByTaskId($taskId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('t.id', 't.title', 'IDENTITY(t.status) as status', 't.description', 'ts.name as statusName', 't.created_at', 't.updated_at', 'u.email')
            ->from('App\Entity\Task', 't')
            ->leftJoin('App\Entity\UserTask', 'ut', 'WITH', 'ut.task_id = t.id')
            ->leftJoin('App\Entity\TaskStatus', 'ts', 'WITH', 't.status = ts.id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'ut.user_id = u.id')
            ->where('ut.task_id = :task_id')
            ->orderBy('t.status', 'ASC')
            ->setParameter('task_id', $taskId)
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    public function findAllTasks()
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('t.id', 't.title', 'ts.name as status', 't.created_at', 'u.email')
            ->from('App\Entity\Task', 't')
            ->leftJoin('App\Entity\TaskStatus', 'ts', 'WITH', 't.status = ts.id')
            ->leftJoin('App\Entity\UserTask', 'ut', 'WITH', 'ut.task_id = t.id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'ut.user_id = u.id')
            ->orderBy('t.status', 'ASC')
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function updateTaskStatus($taskId, $status)
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();

        $q = $qb->update('App\Entity\Task', 't')
            ->set('t.status', ':status')
            ->set('t.updated_at', ":updatedAt")
            ->where('t.id = :task_id')
            ->setParameter('status', $status)
            ->setParameter('task_id', $taskId)
            ->setParameter('updatedAt', new \DateTime())
            ->getQuery();

        return $q->execute();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
