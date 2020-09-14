<?php
/**
 * PHP Version 7.2
 * Admin Service.
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

namespace App\Service;

use App\Entity\Category;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService.
 */
class AdminService
{
    /**
     * Category service.
     *
     * @var CategoryService
     */
    private $categoryService;

    /**
     * Tag service.
     *
     * @var TagService
     */
    private $tagService;
    /**
     * @var PaginatorInterface
     */
    private $paginator;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PostRepository
     */
    private $postRepository;
    /**
     * @var CommentRepository
     */
    private $commentRepository;
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UserService constructor.
     *
     * @param PostRepository               $postRepository    User repository
     * @param PaginatorInterface           $paginator         Paginator
     * @param CommentRepository            $commentRepository
     * @param FileUploader                 $fileUploader
     * @param CategoryService              $categoryService   Category service
     * @param TagService                   $tagService        Tag service
     * @param UserPasswordEncoderInterface $encoder
     * @param UserRepository               $userRepository
     */
    public function __construct(PostRepository $postRepository, PaginatorInterface $paginator, CommentRepository $commentRepository, FileUploader $fileUploader, CategoryService $categoryService, TagService $tagService, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->commentRepository = $commentRepository;
        $this->fileUploader = $fileUploader;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete($user)
    {
        $this->commentRepository->deleteByAuthor($user->getId());
        $postIds = $this->postRepository->findByAuthor($user);
        $this->commentRepository->deleteByPost($postIds);
//        $this->commentRepository->deleteByAuthor($user->getId());
        $this->postRepository->deleteByAuthor($user->getId());
        $this->userRepository->delete($user);
    }

    /**
     * Get categories.
     *
     * @param $user
     * @param $newData
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function change($user, $newData): void
    {

        $newPasswordEncoded = $this->encoder->encodePassword($user, $newData->getPassword());
        $this->userRepository->upgradePassword($user, $newPasswordEncoded);
        $this->userRepository->save($user);
    }
}
