<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/users', name: 'admin.user.')]
class UserAdminController extends AbstractBaseController
{
    public function __construct(private readonly RequestStack $requestStack, private DecoderInterface $decoder, private readonly TranslatorInterface $translator)
    {
        parent::__construct($requestStack, $this->decoder);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $userRepository->findAllMembersQb(),
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/{slug}', name: 'details', methods: ['GET'])]
    public function details(User $user): Response
    {
        return $this->render('admin/user/details.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route(path: '/{slug}/reset', name: 'reset', methods: ['GET'])]
    public function reset(User $user, UserService $userService): Response
    {
        $userService->resetUser($user);
        $this->addCustomFlash('success', $this->translator->trans('Users'), $this->translator->trans('The user "pseudo" has been reset', ['pseudo' => $user->getPseudo()]));

        return $this->redirectToRoute('admin.user.details', [
            'slug' => $user->getSlug(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/{slug}/delete', name: 'delete', methods: ['POST'])]
    public function delete(User $user, UserService $userService, Request $request): Response
    {
        $submittedToken = (string) $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-user', $submittedToken)) {
            if ($this->getUser() === $user) {
                $this->addCustomFlash('danger', $this->translator->trans('Users'), $this->translator->trans("You can't delete yourself!"));

                return $this->redirectToRoute('admin.user.index');
            }

            $request->request->get('deleteData') ? $userService->deleteUser($user, true) : $userService->deleteUser($user);

            $this->addCustomFlash('success', $this->translator->trans('Users'), $this->translator->trans('The user "pseudo" has been deleted', ['pseudo' => $user->getPseudo()]));

            return $this->redirectToRoute('admin.user.index');
        }

        throw new Exception($this->translator->trans('Invalid CSRF token'));
    }
}
