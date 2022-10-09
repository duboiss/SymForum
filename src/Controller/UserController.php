<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserSettingsType;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\ValueObject\Locales;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'user.')]
class UserController extends AbstractBaseController
{
    #[Route(path: '/user/{slug}', name: 'profile', methods: ['GET'])]
    public function profile(User $user, ThreadRepository $threadRepository, MessageRepository $messageRepository): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'lastThreads' => $threadRepository->findLastThreadsByUser($user, 5),
            'lastMessages' => $messageRepository->findLastMessagesByUser($user, 5),
        ]);
    }

    #[Route(path: '/user/{slug}/threads', name: 'threads', methods: ['GET'])]
    public function threads(User $user, ThreadRepository $threadRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $threadRepository->findThreadsByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/threads.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/user/{slug}/messages', name: 'messages', methods: ['GET'])]
    public function messages(User $user, MessageRepository $messageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $messageRepository->findMessagesByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/messages.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/change-locale', name: 'locale', methods: 'GET')]
    public function locale(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $newLocale = $request->getSession()->get('_locale') === Locales::ENGLISH ? Locales::FRENCH : Locales::ENGLISH;

        if ($user = $this->getUser()) {
            $user->setLocale($newLocale);
            $em->flush();
        }

        $request->getSession()->set('_locale', $newLocale);

        return $this->redirectToReferer($request);
    }

    #[Route(path: '/settings', name: 'settings', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function settings(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            if (($userLocale = $user->getLocale()) && $userLocale !== $request->getSession()->get('_locale', Locales::DEFAULT)) {
                $request->getSession()->set('_locale', $userLocale);
            }

            return $this->redirectToRoute('user.settings');
        }

        return $this->renderForm('user/settings.html.twig', [
            'user' => $this->getUser(),
            'form' => $form,
        ]);
    }
}
