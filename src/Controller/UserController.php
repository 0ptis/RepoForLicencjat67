<?php

/**
 * User controller.
 */

namespace App\Controller;

use App\Dto\UserListInputFiltersDto;
use App\Resolver\UserListInputFiltersDtoResolver;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Enum\UserRole;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param UserListInputFiltersDto $filters
     * @param UserRepository          $userRepository
     *
     * @return Response
     */
    #[Route('/admin/users', name: 'admin_user_index', methods: 'GET')]
    public function index(#[MapQueryString(resolver: UserListInputFiltersDtoResolver::class)] UserListInputFiltersDto $filters, UserRepository $userRepository):Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $searchQuery = $filters->searchQuery;
        $users = $userRepository->findUser($searchQuery);

        return $this->render('User/index.html.twig', [
            'users' => $users,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Toggle block action.
     *
     * @param Request                $request
     * @param User                   $user
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    #[Route('/admin/users/{id}/toggle-block', name: 'admin_user_toggle_block', methods: 'POST')]
    public function toggleBlock(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('toggle_block_'.$user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        if ($user === $this->getUser()) {
            return $this->redirectToRoute('admin_user_index');
        }

        $roles = $user->getRoles();

        if (in_array(UserRole::ROLE_BLOCKED->value, $roles, true)) {
            $roles = array_diff($roles, [UserRole::ROLE_BLOCKED->value]);
        } else {
            $roles[] = UserRole::ROLE_BLOCKED->value;
        }

        $user->setRoles(array_unique($roles));
        $em->flush();

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * Blocked page action.
     *
     * @return Response
     */
    #[Route('/blocked', name: 'user_blocked', methods: 'GET')]
    public function blockedPage(): Response
    {
        if (!$this->isGranted(UserRole::ROLE_BLOCKED->value)) {
            return $this->redirectToRoute('home_index');
        }

        return $this->render('User/blocked.html.twig');
    }

    /**
     * View action.
     *
     * @return Response
     */
    #[Route('/profile', name: 'user_view', methods: 'GET')]
    public function view(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        $user = $this->getUser();

        return $this->render('User/view.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Create action.
     *
     * @param Request              $request
     * @param UserServiceInterface $userService
     * @param SluggerInterface     $slugger
     *
     * @return Response
     */
    #[Route('/profile/create', name: 'user_create', methods: ['GET', 'POST'])]
    public function create(Request $request, UserServiceInterface $userService, SluggerInterface $slugger): Response
    {
        $user = new User();
        $user->setRoles([UserRole::ROLE_USER->value]);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $profilePictureFile = $form->get('profilePicture')->getData();

                if ($profilePictureFile) {
                    $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                    try {
                        $profilePictureFile->move(
                            $this->getParameter('profile_pictures_directory'),
                            $newFilename
                        );

                        $user->setProfilePicture($newFilename);
                    } catch (FileException $e) {
                    }
                }

                $plainPassword = $form->get('plainPassword')->getData();
                $userService->createUser($user, $plainPassword);

                return $this->redirectToRoute('user_view');
            }

            if (count($form->get('email')->getErrors()) > 0) {
                $this->addFlash('danger', $this->translator->trans('message.email_already_exists'));
            }
        }
        return $this->render('User/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request              $request
     * @param UserServiceInterface $userService
     * @param SluggerInterface     $slugger
     *
     * @return Response
     */
    #[Route('/profile/edit', name: 'user_edit')]
    public function edit(Request $request, UserServiceInterface $userService, SluggerInterface $slugger): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form->get('profilePicture')->getData();

            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $this->getParameter('profile_pictures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $user->setProfilePicture($newFilename);
            }

            $plainPassword = $form->get('plainPassword')->getData();

            $userService->updateUser($user, $plainPassword);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('User/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
