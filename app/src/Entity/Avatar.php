<?php
/**
 * Avatar.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Avatar.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AvatarRepository")
 * @ORM\Table(
 *     name="avatars",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="UQ_filename_1",
 *              columns={"filename"},
 *          ),
 *     },
 * )
 *
 * @UniqueEntity(
 *      fields={"filename"},
 * )
 */
class Avatar
{
    /**
     * Id.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * User.
     *
     * @var \App\Entity\User
     *
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\User",
     *     inversedBy="avatar",
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="App\Entity\User")
     */
    private $user;

    /**
     * Filename.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=191,
     * )
     *
     * @Assert\Type(type="string")
     */
    private $filename;

    /**
     * Getter for id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for user.
     *
     * @return \App\Entity\User|null User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Setter for user.
     *
     * @param \App\Entity\User $user User
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * Getter for filename.
     *
     * @return string Filename
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Setter for filename.
     *
     * @param string $filename Filename
     */
    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}
