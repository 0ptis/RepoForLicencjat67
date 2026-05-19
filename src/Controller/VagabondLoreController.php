<?php

/**
 * Vagabond lore controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class VagabondLoreController.
 */
#[Route('/story')]
class VagabondLoreController extends AbstractController
{
    /**
     * Index action.
     *
     * @return Response
     */
    #[Route(name: 'story_index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('Story/index.html.twig');
    }
}
