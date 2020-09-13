<?php
/**
 * Registration service.
 */

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;

/**
 * Class RegistrationService.
 */
class RegistrationService
{
    private $userRepository;
    private $passwordEncoder;

    /**
     * RegistrationService constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        UserRepository $userRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $user
     * @param $formdata
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register($user, $formdata)
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $formdata
            )
        );

        $this->userRepository->save($user);
    }
}
