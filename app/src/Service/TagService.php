<?php
/**
 * PHP Version 7.2
 * Tag Service.
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
     *
     * @return \App\Entity\Tag|null Tag entity
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneById(int $tagId): ?Tag
    {
        return $this->tagRepository->findOneById($tagId);
    }
}
