<?php

/**
 * Game controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class GameController.
 */
#[Route('/game')]
class GameController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response
     */
    #[Route(name: 'game_index', methods: 'GET')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        return $this->render('Game/index.html.twig');
    }
}
