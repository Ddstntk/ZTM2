<?php
/**
 * Admin Dashboard controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminDashboardController.
 *
 * @Route("/admin")
 */
class AdminDashboardController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\UserRepository            $UserRepository User repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/index",
     *     methods={"GET"},
     *     name="admin_index",
     * )
     */
    public function index(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->queryAll(),
            $request->query->getInt('page', 1),
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'admin/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param \App\Entity\User $user User entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/user/",
     *     methods={"GET"},
     *     name="admin_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     */
    public function show(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->query->getInt('id');
        $user = $userRepository->findOneBy(['id' => $id]);

        return $this->render(
            'admin/show.html.twig',
            ['user' => $user]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\User                          $user           User entity
     * @param \App\Repository\UserRepository            $userRepository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_edit",
     * )
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime());
            $userRepository->save($user);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_index');
        }

        return $this->render(
            'admin/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\User                          $user           User entity
     * @param \App\Repository\UserRepository            $userRepository User repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="user_delete",
     * )
     */
    public function delete(
        Request $request,
        User $user,
        UserRepository $userRepository,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ): Response {
        $form = $this->createForm(UserType::class, $user, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->deleteByAuthor($user->getId());
            $post_ids = $postRepository->queryByAuthor($user);
            $commentRepository->deleteByPost($post_ids);
            $postRepository->deleteByAuthor($user->getId());
            $userRepository->delete($user);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('admin_index');
        }

        return $this->render(
            'admin/delete.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Index comments.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request   HTTP request
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/comment_index",
     *     methods={"GET"},
     *     name="admin_index_comments",
     * )
     */
    public function indexComments(
        Request $request,
        PaginatorInterface $paginator,
        CommentRepository $commentRepository
    ): Response {
        $pagination = $paginator->paginate(
            $commentRepository->findAll(),
            $request->query->getInt('page', 1),
            commentRepository::PAGINATOR_ITEMS_PER_PAGE
        );

        return $this->render(
            'admin/comments.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/comment/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     name="comment_delete",
     * )
     */
    public function deleteComment(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $commentRepository->deleteById($comment->getId());

        return $this->redirectToRoute('admin_index_comments');
    }
}
