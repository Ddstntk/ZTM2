<?php
/**
 * PHP Version 7.2
 * Avatar Service.
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

use App\Entity\Avatar;
use App\Repository\AvatarRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AvatarService.
 */
class AvatarService
{
    /**
     * Avatar repository.
     *
     * @var AvatarRepository
     */
    private $avatarRepository;

    /**
     * File uploader.
     *
     * @var FileUploader;
     */
    private $fileUploader;

    /**
     * Filesystem.
     *
     * @var;
     */
    private $filesystem;

    /**
     * AvatarService constructor.
     *
     * @param AvatarRepository $avatarRepository Avatar repository
     * @param Filesystem       $filesystem       Filesystem component
     * @param FileUploader     $fileUploader
     */
    public function __construct(AvatarRepository $avatarRepository, Filesystem $filesystem, FileUploader $fileUploader)
    {
        $this->avatarRepository = $avatarRepository;
        $this->fileUploader = $fileUploader;
        $this->filesystem = $filesystem;
    }

    /**
     * Find avatar by Id.
     *
     * @param int $avatarId
     *
     * @return Avatar|null Avatar entity
     */
    public function findOneById(int $avatarId): ?Avatar
    {
        return $this->avatarRepository->findOneBy(['id' => $avatarId]);
    }

    /**
     * Find all.
     *
     * @return Avatar[]
     */
    public function findAll(): array
    {
        return $this->avatarRepository->findAll();
    }

    /**
     * Create new.
     *
     * @param $form
     * @param $avatar
     * @param $user
     *
     * @return bool
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createAvatar($form, $avatar, $user): bool
    {
        $avatarFilename = $this->fileUploader->upload(
            $form->get('file')->getData()
        );
        $avatar->setUser($user);
        $avatar->setFilename($avatarFilename);
        $this->avatarRepository->save($avatar);

        return true;
    }

    /**
     * Edit.
     *
     * @param $form
     * @param $avatar
     * @param $filename
     *
     * @return bool
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function editAvatar($form, $avatar, $filename): bool
    {
        $this->filesystem->remove(
            $filename
        );
        $avatarFilename = $this->fileUploader->upload(
            $form->get('file')->getData()
        );
        $avatar->setFilename($avatarFilename);
        $this->avatarRepository->save($avatar);

        return true;
    }
}
