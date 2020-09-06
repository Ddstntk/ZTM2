<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{

    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    const PAGINATOR_ITEMS_PER_PAGE = 10;


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
     * Save record.
     *
     * @param \App\Entity\Post $post Post entity
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Comment $comment): void
    {
        $this->_em->persist($comment);
        $this->_em->flush($comment);
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
    /**
     * Delete by Id.
     *
     * @param $postIds
     */
    public function deleteById($id): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
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

    /**
     * Get or create new query builder.
     *
     * @param \Doctrine\ORM\QueryBuilder|null $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('category');
    }

    /**
     * Query all comments.
     *
     * @return \Doctrine\ORM\QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('comment.created_at', 'DESC');
    }
}
