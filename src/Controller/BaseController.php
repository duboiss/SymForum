<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class BaseController extends AbstractController
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    protected function getUser(): User
    {
        return parent::getUser();
    }

    /**
     * @param string $type
     * @param string $title
     * @param string $content
     */
    protected function addCustomFlash(string $type, string $title, string $content)
    {
        $this->session->getFlashBag()->add($type, ['title' => $title, 'content' => $content]);
    }
}