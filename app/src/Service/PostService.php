<?php
/**
 * Post service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PostService.
 */
class PostService
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
     * @var PostRepository
     */
    private $postRepository;
    private $commentRepository;
    private $fileUploader;

    /**
     * PostService constructor.
     *
     * @param PostRepository $postRepository Post repository
     * @param PaginatorInterface $paginator Paginator
     * @param CommentRepository $commentRepository
     * @param FileUploader $fileUploader
     * @param CategoryService $categoryService Category service
     * @param TagService $tagService Tag service
     */
    public function __construct(
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        CommentRepository $commentRepository,
        FileUploader $fileUploader,
        CategoryService $categoryService,
        TagService $tagService
    ) {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->commentRepository = $commentRepository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Prepare filters for the posts list.
     *
     * @param array $filters Raw filters from request
     *
     * @return array Result array of filters
     */
    private function prepareFilters(array $filters): array
    {
        $resultFilters = [];
        if (isset($filters['category_id']) && is_numeric($filters['category_id'])) {
            $category = $this->categoryService->findOneById(
                $filters['category_id']
            );
            if (null !== $category) {
                $resultFilters['category'] = $category;
            }
        }

        return $resultFilters;
    }

    /**
     * Create paginated list.
     *
     * @param int                                                 $page    Page number
     * @param UserInterface $user    User entity
     * @param array                                               $filters Filters array
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page, array $filters = []): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->postRepository->queryAll($filters),
            $page,
            PostRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get categories.
     *
     * @return Category[] Paginated list
     */
    public function getCategories(): array
    {
        return
            $this->categoryService->findAll();
    }

    /**
     * @param $comment
     * @param $post
     */
    public function addComment($comment, $post, $user)
    {
        $comment->setUserId($user);
        $comment->setPostId($post);
        $this->commentRepository->save($comment);
    }

    /**
     * @param $post
     * @param $formfile
     * @param $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createPost($post, $formfile, $user)
    {
        $imageFilename = $this->fileUploader->upload(
            $formfile
        );
        $post->setImage($imageFilename);

        $post->setAuthor($user);
        $this->postRepository->save($post);
    }

    /**
     * @param $post
     * @param $formfile
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editPost($post, $formfile)
    {
        $imageFilename = $this->fileUploader->upload(
            $formfile
        );
        $post->setImage($imageFilename);
        $this->postRepository->save($post);
    }

    /**
     * @param $post
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete($post)
    {
        $this->postRepository->delete($post);
    }
}
