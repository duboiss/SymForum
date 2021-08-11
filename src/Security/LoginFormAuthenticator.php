<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'security.login';
    public const REDIRECT_AFTER_LOGIN_ROUTE = 'forum.index';

    private FlashBagInterface $flashBag;

    public function __construct(private RouterInterface $router, private CsrfTokenManagerInterface $csrfTokenManager, RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate(self::LOGIN_ROUTE);
    }

    public function authenticate(Request $request): Passport
    {
        $email = (string) $request->request->get('email');
        $password = (string) $request->request->get('password');
        $csrfToken = (string) $request->request->get('_csrf_token');

        return new Passport(new UserBadge($email), new PasswordCredentials($password), [
            new RememberMeBadge(),
            new CsrfTokenBadge('authenticate', $csrfToken),
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        if (($targetUrl = $request->request->get('targetUrl')) && preg_match('@^/forums@', (string) $targetUrl)) {
            return new RedirectResponse((string) $targetUrl);
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate(self::REDIRECT_AFTER_LOGIN_ROUTE));
    }
}
