<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @method User|null getUser()
 */
abstract class BaseController extends AbstractController
{
    private FlashbagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $content
     */
    protected function addCustomFlash(string $type, string $title, string $content): void
    {
        $this->flashBag->add($type, ['title' => $title, 'content' => $content]);
    }
}