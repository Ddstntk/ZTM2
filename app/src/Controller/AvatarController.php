<?php
/**
 * Avatar controller.
 */

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use App\Repository\AvatarRepository;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AvatarController.
 *
 * @Route("/avatar")
 */
class AvatarController extends AbstractController
{
    /**
     * Avatar repository.
     *
     * @var \App\Repository\AvatarRepository
     */
    private $avatarRepository;

    /**
     * File uploader.
     *
     * @var \App\Service\FileUploader
     */
    private $fileUploader;
    private $filesystem;


    /**
     * AvatarController constructor.
     *
     * @param \App\Repository\AvatarRepository         $avatarRepository Avatar repository
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem       Filesystem component
     * @param \App\Service\FileUploader                $fileUploader     File uploader
     */
    public function __construct(AvatarRepository $avatarRepository, Filesystem $filesystem, FileUploader $fileUploader)
    {
        $this->avatarRepository = $avatarRepository;
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/create",
     *     name="avatar_create",
     *     methods={"GET", "POST"}
     * )
     */
    public function create(Request $request): Response
    {
        $avatar = new Avatar();
        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFilename = $this->fileUploader->upload(
                $form->get('file')->getData()
            );
            $avatar->setUser($this->getUser());
            $avatar->setFilename($avatarFilename);
            $this->avatarRepository->save($avatar);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'avatar/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Create action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Avatar                        $avatar  Avatar
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/edit",
     *     name="avatar_edit",
     *     methods={"GET", "POST"}
     * )
     */
    public function edit(Request $request): Response
    {
        if (!$this->getUser()->getAvatar()) {
            return $this->redirectToRoute('avatar_create');
        }

        $avatar = $this->getUser()->getAvatar();
        var_dump($this->filesystem);
        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->filesystem->remove(
                $this->getParameter('avatars_directory').'/'.$avatar->getFilename()
            );
            $avatarFilename = $this->fileUploader->upload(
                $form->get('file')->getData()
            );
            $avatar->setFilename($avatarFilename);
            $this->avatarRepository->save($avatar);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('post_index');
        }

        return $this->render(
            'avatar/edit.html.twig',
            [
                'form' => $form->createView(),
                'avatar' => $avatar,
            ]
        );
    }
}
