<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
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



 /**
  * @return Comment[] Returns an array of Comment objects
  */


    /**
     * @param $postId
     * @return int|mixed|string
     */
    public function findByPost($postId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.post_id = :val')
            ->setParameter('val', $postId)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Delete by author record.
     *
     * @param $userId
     */
    public function deleteByAuthor($userId): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.user_id = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->execute()
        ;
    }

    /**
     * Delete by author record.
     *
     * @param $postIds
     */
    public function deleteByPost($postIds): void
    {

        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.post_id in (:val)')
            ->setParameter('val', $postIds)
            ->getQuery()
            ->execute()
        ;

    }

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
