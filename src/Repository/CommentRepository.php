<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentByTaskIdUserId($taskId, $userId)
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();

        $query = $qb
            ->select('c.comment', 'c.created_at', 'u.email')
            ->from('App\Entity\Comment', 'c')
            ->where('c.user_id = :user_id')
            ->andWhere('c.task_id = :task_id')
            ->leftJoin('App\Entity\User', 'u', 'WITH', 'c.user_id = u.id')
            ->orderBy('c.created_at', 'DESC')
            ->setParameter('user_id', $userId)
            ->setParameter('task_id', $taskId)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function createComment($taskId, $userId, $comm)
    {
        $entityManager = $this->getEntityManager();

        $comment = new Comment();
        $comment->setUserId($userId);
        $comment->setTaskId($taskId);
        $comment->setComment($comm);

        $entityManager->persist($comment);
        $entityManager->flush();
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
