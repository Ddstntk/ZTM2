<?php
/**
 * Record controller.
 */

namespace App\Controller;

use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RecordController.php.
 * @package App\Controller
 *
 */
class RecordController extends AbstractController
{

    /**
     * Show action.
     *
     * @param \App\Repository\RecordRepository $repository Record repository
     * @param int                              $id         Record id
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     */
    public function show(RecordRepository $repository, int $id): Response
    {
        return $this->render(
            'record/show.html.twig',
            ['item' => $repository->findById($id)]
        );
    }


    /**
     * Index action.
     *
     * @param \App\Repository\RecordRepository $repository Record repository
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     */
    public function index(RecordRepository $repository): Response
    {
        return $this->render(
            'record/index.html.twig',
            ['data' => $repository->findAll()]
        );
    }
}