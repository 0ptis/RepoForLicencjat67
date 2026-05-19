<?php

/**
 * Patch notes controller.
 */

namespace App\Controller;

use App\Dto\PatchNotesFiltersDto;
use App\Resolver\PatchNotesFiltersDtoResolver;
use App\Entity\PatchNotes;
use App\Form\Type\PatchNotesType;
use App\Service\PatchNotesServiceInterface;
use App\Repository\PatchNotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Security\Voter\PatchNotesVoter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PatchNotesController.
 */
#[Route('/PatchNotes')]
class PatchNotesController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param PatchNotesServiceInterface $patchNotesService
     * @param TranslatorInterface        $translator
     */
    public function __construct(private readonly PatchNotesServiceInterface $patchNotesService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/create', name: 'patch_notes_create', methods: ['GET', 'POST'])]
    #[IsGranted(PatchNotesVoter::CREATE)]
    public function create(Request $request): Response
    {
        $patchNotes = new PatchNotes();
        $form = $this->createForm(PatchNotesType::class, $patchNotes);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->patchNotesService->save($patchNotes);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('patch_notes_view');
        }

        return $this->render('PatchNotes/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Index action.
     *
     * @param PatchNotesRepository      $repository
     * @param PatchNotesFiltersDto|null $filters
     *
     * @return Response HTTP response
     */
    #[Route('/', name: 'patch_notes_view', methods: ['GET'])]
    public function index(PatchNotesRepository $repository, #[MapQueryString(resolver: PatchNotesFiltersDtoResolver::class)] ?PatchNotesFiltersDto $filters = null): Response
    {
        $filters = $filters ?? new PatchNotesFiltersDto();
        $notes = $repository->findByFilters($filters);

        return $this->render('PatchNotes/view.html.twig', [
            'notes' => $notes,
            'filters' => $filters,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request    $request
     * @param PatchNotes $patchNotes
     *
     * @return Response
     */
    #[Route('/{id}/delete', name: 'patch_notes_delete', methods: ['POST'])]
    #[IsGranted(PatchNotesVoter::DELETE, subject: 'patchNotes')]
    public function delete(Request $request, PatchNotes $patchNotes): Response
    {
        if ($this->isCsrfTokenValid('delete'.$patchNotes->getId(), $request->request->get('_token'))) {
            $this->patchNotesService->delete($patchNotes);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));
        }

        return $this->redirectToRoute('patch_notes_view');
    }
}
