<?php
/**
 * Tag service.
 */

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;

/**
 * Class TagService.
 */
class TagService
{
    /**
     * Tag repository.
     *
     * @var \App\Repository\TagRepository
     */
    private $tagRepository;

    /**
     * TagService constructor.
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Find tag by Id.
     *
     * @param int $id Tag Id
     *
     * @return \App\Entity\Tag|null Tag entity
     */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->findOneById($id);
    }
}
