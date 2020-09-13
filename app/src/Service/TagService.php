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
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * TagService constructor.
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Find tag by Id.
     *
     * @param int $tagId
     * @return \App\Entity\Tag|null Tag entity
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById(int $tagId): ?Tag
    {
        return $this->tagRepository->findOneById($tagId);
    }
}
