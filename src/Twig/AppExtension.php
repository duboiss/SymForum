<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private RequestStack $request)
    {
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('login_target_path', [$this, 'loginTargetPath']),
        ];
    }

    public function loginTargetPath(): string
    {
        if ($masterRequest = $this->request->getMasterRequest()) {
            return $this->urlGenerator->generate('security.login', ['redirect' => $masterRequest->getRequestUri()]);
        }

        return $this->urlGenerator->generate('security.login');
    }
}
