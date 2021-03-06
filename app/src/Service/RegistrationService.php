<?php
/**
 * PHP Version 7.2
 * Registration Service.
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

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationService.
 */
class RegistrationService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * RegistrationService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository               $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param $user
     * @param $formdata
     *
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
