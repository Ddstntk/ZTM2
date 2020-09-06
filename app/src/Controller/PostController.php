<?php
/**
 * Post controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\AvatarRepository;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use App\Service\PostService;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController.
 *
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * File uploader.
     *
     * @var \App\Service\FileUploader
     */
    private $fileUploader;
    private $filesystem;
    private $postService;


    /**
     * PostController constructor.
     *
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem       Filesystem component
     * @param \App\Service\FileUploader                $fileUploader     File uploader
     */
    public function __construct(Filesystem $filesystem, FileUploader $fileUploader, PostService $postService)
    {
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
        $this->postService = $postService;
    }


    /**
     * Index action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\PostRepository            $postRepository Post repository
     * @param \Knp\Component\Pager\PaginatorInterface   $paginator      Paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="post_index",
     * )
     */
    public function index(Request $request): Response
    {
//        $pagination = $paginator->paginate(
////            $postRepository->queryByAuthor($this->getUser()),
//            $postRepository->queryAll(),
//            $request->query->getInt('page', 1),
//            PostRepository::PAGINATOR_ITEMS_PER_PAGE
//        );
////        var_dump($pagination);
//        return $this->render(
//            'post/index.html.twig',
//            ['pagination' => $pagination]
//        );


        $filters = [];
        $filters['category_id'] = $request->query->getInt('filters_category_id');
        $filters['tag_id'] = $request->query->getInt('filters_tag_id');
        $category = $this->postService->getCategories();
//        var_dump($category);
        $pagination = $this->postService->createPaginatedList(
            $request->query->getInt('page', 1),
            $this->getUser(),
            $filters
        );
        return $this->render(
            'post/index.html.twig',
            ['pagination' => $pagination,
                'category'=>$category]
        );
    }

    /**
     * Show action.
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Post $post Post entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @Route(
     *     "/{id}",
     *     methods={"GET", "POST"},
     *     name="post_show",
     *     requirements={"id": "[1-9]\d*"},
     * )
     *
     */
    public function show(Request $request, Post $post, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUserId($this->getUser());
            $comment->setPostId($post);
            $commentRepository->save($comment);
            $this->addFlash('success', 'message_created_successfully');

//            return $this->redirectToRoute('post_show');
        }

        $post_id = $post->getId();
        $comments = $commentRepository->findByPost($post_id);

        return $this->render(
            'post/show.html.twig',
            ['post' => $post,
                'comments'=>$comments,
                'form'=>$form->createView()]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\PostRepository            $postRepository Post repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     methods={"GET", "POST"},
     *     name="post_create",
     * )
     *
     */
    public function create(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFilename = $this->fileUploader->upload(
                $form->get('file')->getData()
            );
            $post->setImage($imageFilename);

            $post->setAuthor($this->getUser());
            $postRepository->save($post);
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Comment action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Repository\PostRepository            $postRepository Post repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/{id}/comment",
     *     methods={"GET", "POST"},
     *     name="comment_create",
     * )
     *
     */
    public function comment($id, Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();

        $form = $this->createForm(Comment::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUserId($this->getUser());

            $commentRepository->save($comment);
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('post_show');
        }

        return $this->render(
            'post/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Post                          $post           Post entity
     * @param \App\Repository\PostRepository            $postRepository Post repository
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
     *     name="post_edit",
     * )
     * @IsGranted(
     *     "EDIT",
     *     subject="post",
     * )
     */
    public function edit(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFilename = $this->fileUploader->upload(
                $form->get('file')->getData()
            );
            $post->setImage($imageFilename);
            $postRepository->save($post);
            $this->addFlash('success', 'message_updated_successfully');
//            var_dump($post);
            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/edit.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request        HTTP request
     * @param \App\Entity\Post                          $post           Post entity
     * @param \App\Repository\PostRepository            $postRepository Post repository
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
     *     name="post_delete",
     * )
     * @IsGranted(
     *     "DELETE",
     *     subject="post",
     * )
     */
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(FormType::class, $post, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->delete($post);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'post/delete.html.twig',
            [
                'form' => $form->createView(),
                'post' => $post,
            ]
        );
    }
}
