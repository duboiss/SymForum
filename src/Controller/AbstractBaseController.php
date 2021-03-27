<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * @method User|null getUser()
 */
abstract class AbstractBaseController extends AbstractController
{
    private FlashBagInterface $flashBag;

    public function __construct(SessionInterface $session, private DecoderInterface $decoder)
    {
        $this->flashBag = $session->getFlashBag();
    }

    protected function addCustomFlash(string $type, string $title, string $content): void
    {
        $this->flashBag->add($type, ['title' => $title, 'content' => $content]);
    }

    protected function redirectToReferer(Request $request): RedirectResponse
    {
        return $this->redirect($request->headers->get('referer') ?? $this->generateUrl('forum.index'));
    }

    protected function jsonDecodeRequestContent(Request $request): array
    {
        return $this->decoder->decode($request->getContent(), 'json');
    }
}
