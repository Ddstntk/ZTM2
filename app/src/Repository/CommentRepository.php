<?php
/**
 * PHP Version 7.2
 * Comment Repository.
 *
 * @category  Social_Network
 *
 * @author    Konrad Szewczuk <konrad3szewczuk@gmail.com>
 *
 * @copyright 2020 Konrad Szewczuk
 *
 * @license   https://opensource.org/licenses/MIT MIT license
 *
 * @see      wierzba.wzks.uj.edu.pl/~16_szewczuk
 */

namespace App\Repository;

use App\Entity\Comment;
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

    /**
     * CommentRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Comment $comment
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
     * @param $postId
     *
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
     * @param $comId
     */
    public function deleteById($comId): void
    {
        $this->createQueryBuilder('c')
            ->delete()
            ->andWhere('c.id = :val')
            ->setParameter('val', $comId)
            ->getQuery()
            ->execute()
        ;
    }


    /**
     * Query all comments.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->orderBy('comment.created_at', 'DESC');
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('category');
    }
}
