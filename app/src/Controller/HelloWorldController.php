<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HelloWorldController
 * @package App\Controller
 */
class HelloWorldController extends AbstractController
{
    /**
     * @return Response
     * @throws \Exception
     */
    public function index(Request $request, ?string $name = 'Jan Paweł II papież'): Response
    {
        $name = $request->query->get('name') ?? $name;
        return $this->render(
            'hello-world/index.html.twig',
            ['name' => $name]
        );
    }
}
