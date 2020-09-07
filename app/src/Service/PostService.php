<?php
/**
 * Post service.
 */

namespace App\Service;

use App\Entity\Post;
use App\Repository\PostRepository;
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
     * @var \App\Service\CategoryService
     */
    private $categoryService;

    /**
     * Tag service.
     *
     * @var \App\Service\TagService
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

    /**
     * PostService constructor.
     *
     * @param \App\Repository\PostRepository          $postRepository  Post repository
     * @param \Knp\Component\Pager\PaginatorInterface $paginator       Paginator
     * @param \App\Service\CategoryService            $categoryService Category service
     * @param \App\Service\TagService                 $tagService      Tag service
     */
    public function __construct(
        PostRepository $postRepository,
        PaginatorInterface $paginator,
        CategoryService $categoryService,
        TagService $tagService
    ) {
        $this->postRepository = $postRepository;
        $this->paginator = $paginator;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
    }

//    /**
//     * Find post by Id.
//     *
//     * @param int $id Post Id
//     *
//     * @return \App\Entity\Post|null Post entity
//     */
//    public function findOneById(int $id): ?Post
//    {
//        return $this->postRepository->findOneById($id);
//    }

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

//        if (isset($filters['tag_id']) && is_numeric($filters['tag_id'])) {
//            $tag = $this->tagService->findOneById($filters['tag_id']);
//            if (null !== $tag) {
//                $resultFilters['tag'] = $tag;
//            }
//        }

        return $resultFilters;
    }

    /**
     * Create paginated list.
     *
     * @param int                                                 $page    Page number
     * @param \Symfony\Component\Security\Core\User\UserInterface $user    User entity
     * @param array                                               $filters Filters array
     *
     * @return \Knp\Component\Pager\Pagination\PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page, UserInterface $user, array $filters = []): PaginationInterface
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
     * @return \App\Entity\Category[] Paginated list
     */
    public function getCategories(): array
    {
        return
            $this->categoryService->findAll();
    }
}
