<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{
    /** @var FlashBagInterface */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $content
     */
    protected function addCustomFlash(string $type, string $title, string $content)
    {
        $this->flashBag->add($type, ['title' => $title, 'content' => $content]);
    }
}