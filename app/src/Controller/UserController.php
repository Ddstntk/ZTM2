<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController.
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * Show action.
     *
     * @param \App\Entity\User $user User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/me",
     *     methods={"GET"},
     *     name="user_show",
     * )
     */
    public function show(): Response
    {
        return $this->render(
            'user/show.html.twig'
        );
    }

//    /**
//     * Create action.
//     *
//     * @param \Symfony\Component\HttpFoundation\Request $request            HTTP request
//     * @param \App\Repository\UserRepository        $userRepository User repository
//     *
//     * @return \Symfony\Component\HttpFoundation\Response HTTP response
//     *
//     * @throws \Doctrine\ORM\ORMException
//     * @throws \Doctrine\ORM\OptimisticLockException
//     *
//     * @Route(
//     *     "/register",
//     *     methods={"GET", "POST"},
//     *     name="user_create",
//     * )
//     */
//    public function create(Request $request, UserRepository $userRepository): Response
//    {
//        $user = new User();
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
    ////            $user->setCreatedAt(new \DateTime());
    ////            $user->setUpdatedAt(new \DateTime());
//            $userRepository->save($user);
//
//            $this->addFlash('success', 'message_created_successfully');
//
//            return $this->redirectToRoute('user_index');
//        }
//
//        return $this->render(
//            'user/create.html.twig',
//            ['form' => $form->createView()]
//        );
//    }

    /**
     * Change password action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\UserRepository            $userRepository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/password",
     *     methods={"GET", "POST"},
     *     name="user_passwd",
     * )
     */
    public function changePassword(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $user->setUpdatedAt(new \DateTime());
//            $userRepository->save($user);

            $newData = $form->getData();
            $oldPassword = $newData['password'];
            $newPassword = $newData['plainPassword'];
            $isPassValid = $encoder->isPasswordValid($user, $oldPassword, $user->getSalt());

            if ($isPassValid) {
                $newPasswordEncoded = $encoder->encodePassword($user, $newPassword);
                $userRepository->upgradePassword($user, $newPasswordEncoded);
                $result = true;
            } else {
                $result = false;
                $this->addFlash('danger', 'message_wrong_password');
            }
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_show');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param User                                      $user
     * @param \App\Repository\UserRepository            $userRepository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route(
     *     "/edit",
     *     methods={"GET", "EDIT"},
     *     name="user_edit",
     * )
     */
    public function edit(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['method' => 'EDIT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_show');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
