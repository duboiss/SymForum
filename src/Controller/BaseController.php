<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method User|null getUser()
 */
abstract class BaseController extends AbstractController
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $content
     */
    protected function addCustomFlash(string $type, string $title, string $content): void
    {
        $this->session->getFlashBag()->add($type, ['title' => $title, 'content' => $content]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    protected function redirectToReferer(Request $request): RedirectResponse
    {
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('forum.index'));
    }
}
