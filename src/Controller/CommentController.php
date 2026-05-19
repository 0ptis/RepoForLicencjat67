<?php

/**
 * Comment controller.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Security\Voter\CommentVoter;
use App\Service\CommentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CommentController.
 */
#[Route('/comment')]
class CommentController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param CommentServiceInterface $commentService
     * @param TranslatorInterface     $translator
     */
    public function __construct(private readonly CommentServiceInterface $commentService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Edit action.
     * @param Request $request
     * @param Comment $comment
     *
     * @return Response
     */
    #[Route('/{id}/edit', name: 'comment_edit', requirements: ['id' => '[1-9]\d*'], methods: ['POST'])]
    public function edit(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::EDIT, $comment);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('edit_comment_'.$comment->getId(), $token)) {
            throw new InvalidCsrfTokenException();
        }

        $content = trim((string) $request->request->get('content'));

        if ('' !== $content) {
            $comment->setContent($content);
            $this->commentService->save($comment);
            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));
        } elseif ('' === $content) {
            $this->addFlash('danger', $this->translator->trans('message.cannot_be_empty'));
        }

        return $this->redirectToRoute('thread_view', [
            'id' => $comment->getThread()->getId(),
            '_fragment' => 'comment-'.$comment->getId(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request
     * @param Comment $comment
     *
     * @return Response
     */
    #[Route('/{id}/delete', name: 'comment_delete', requirements: ['id' => '[1-9]\d*'], methods: ['POST'])]
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted(CommentVoter::DELETE, $comment);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('delete_comment_'.$comment->getId(), $token)) {
            throw new InvalidCsrfTokenException();
        }

        $threadId = $comment->getThread()->getId();
        $this->commentService->delete($comment);

        $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

        return $this->redirectToRoute('thread_view', ['id' => $threadId]);
    }
}
