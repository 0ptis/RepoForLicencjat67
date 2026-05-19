<?php

/**
 * Home controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class HomeController.
 */
#[Route('/home')]
class HomeController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response
     */
    #[Route(name: 'home_index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('Home/index.html.twig');
    }
}
