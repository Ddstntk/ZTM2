<?php
/**
 * Category service.
 */

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CategoryService.
 */
class CategoryService
{
    /**
     * Category repository.
     *
     * @var CategoryRepository
     */
    private $categoryRepository;

    private $paginator;
    /**
     * CategoryService constructor.
     *
     * @param CategoryRepository $categoryRepository Category repository
     * @param PaginatorInterface $paginator          Paginator
     */
    public function __construct(CategoryRepository $categoryRepository, PaginatorInterface $paginator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->paginator = $paginator;
    }

    /**
     * Find category by Id.
     *
     * @param int $id Category Id
     *
     * @return Category|null Category entity
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->findOneBy(['id' => $id]);
    }

    /**
     * Find all.
     *
     * @return Category[]
     */
    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @param $page
     * @return PaginationInterface
     */
    public function createPaginatedList($page)
    {
        return $this->paginator->paginate(
            $this->categoryRepository->queryAll(),
            $page,
            CategoryRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * @param $category
     * @return bool
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createCategory($category):bool
    {
        $this->categoryRepository->save($category);
        return true;
    }

    /**
     * @param $category
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editCategory($category):bool
    {
        $category->setUpdatedAt(new \DateTime());
        $this->categoryRepository->save($category);
        return true;
    }

    /**
     * @param $category
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete($category)
    {
        $this->categoryRepository->delete($category);
    }
}
