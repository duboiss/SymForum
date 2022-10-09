<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private RequestStack $request)
    {
    }

    public function loginTargetPath(): string
    {
        if ($masterRequest = $this->request->getCurrentRequest()) {
            return $this->urlGenerator->generate('security.login', ['redirect' => $masterRequest->getRequestUri()]);
        }

        return $this->urlGenerator->generate('security.login');
    }
}
