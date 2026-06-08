<?php

/**
 * Thread controller.
 */

namespace App\Controller;

use App\Entity\Thread;
use App\Entity\User;
use App\Form\Type\CommentType;
use App\Form\Type\ThreadType;
use App\Service\ThreadServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Voter\ThreadVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Dto\ThreadListInputFiltersDto;
use App\Resolver\ThreadListInputFiltersDtoResolver;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;

/**
 * Class ThreadController.
 */
#[Route('/Thread')]
class ThreadController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param ThreadServiceInterface $threadService
     * @param TranslatorInterface    $translator
     */
    public function __construct(private readonly ThreadServiceInterface $threadService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param ThreadListInputFiltersDto $filters
     * @param int                       $page
     *
     * @return Response
     */
    #[Route(name: 'thread_index', methods: ['GET'])]
    public function index(#[MapQueryString(resolver: ThreadListInputFiltersDtoResolver::class)] ThreadListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->threadService->getPaginatedList($page, $filters);

        return $this->render('Thread/index.html.twig', [
            'pagination' => $pagination,
            'filters' => $filters,
        ]);
    }

    /**
     * View action.
     *
     * @param Request                $request
     * @param Thread                 $thread
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route('/{id}', name: 'thread_view', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function view(Request $request, Thread $thread, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            $comment->setThread($thread);
            $user = $this->getUser();
            $comment->setAuthor($user);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('thread_view', ['id' => $thread->getId()]);
        }

        return $this->render('Thread/view.html.twig', [
            'thread' => $thread,
            'comment_form' => $form->createView(),
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/create', name: 'thread_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $thread = new Thread();
        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        /** @var User $user */
        $user = $this->getUser();
        $thread->setAuthor($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->threadService->save($thread);
            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('thread_index');
        }

        return $this->render('Thread/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit action.
     *
     * @param Request $request
     * @param Thread  $thread
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'thread_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Thread $thread): Response
    {
        $this->denyAccessUnlessGranted(ThreadVoter::EDIT, $thread);
        $form = $this->createForm(ThreadType::class, $thread, [
            'method' => 'POST',
            'action' => $this->generateUrl('thread_edit', ['id' => $thread->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->threadService->save($thread);
            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('thread_index');
        }

        return $this->render('Thread/edit.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request
     * @param Thread  $thread
     *
     * @return Response
     */
    #[Route('/{id}/delete', name: 'thread_delete', requirements: ['id' => '[1-9]\d*'], methods: ['POST'])]
    public function delete(Request $request, Thread $thread): Response
    {
        $this->denyAccessUnlessGranted(ThreadVoter::DELETE, $thread);
        if ($this->isCsrfTokenValid('delete'.$thread->getId(), $request->request->get('_token'))) {
            $this->threadService->delete($thread);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
        }

        return $this->redirectToRoute('thread_index');
    }
}
