<?php

/**
 * Error controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ErrorController.
 */
#[AsController]
class ErrorController extends AbstractController
{
    /**
     * Show action.
     *
     * @param FlattenException $exception
     * @param Request          $request
     *
     * @return Response
     */
    #[Route(path: '/_error/{code}', name: 'app_error')]
    public function show(FlattenException $exception, Request $request): Response
    {
        $code = $exception->getStatusCode();

        return $this->render('error/error.html.twig', [
            'status_code' => $code,
            'status_text' => Response::$statusTexts[$code] ?? 'Error',
        ]);
    }
}
