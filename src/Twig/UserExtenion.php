<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtenion extends AbstractExtension
{
    private const ROLES = [
        'ROLE_ADMIN' => 'Administrateur',
        'ROLE_MODERATOR' => 'Modérateur',
    ];

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_profile_link', [$this, 'getUserProfileLink'], ['is_safe' => ['html']]),
            new TwigFunction('user_profile_role', [$this, 'getUserProfileRole']),
        ];
    }

    public function getUserProfileLink(?User $user, string $text = null, string $class = null): string
    {
        if ($user) {
            $route = $this->urlGenerator->generate('user.profile', ['slug' => $user->getSlug()]);
            $classAttr = $class ? ' class="' . $class . '"' : '';

            return sprintf('<a href="%s"' . $classAttr . '>%s</a>', $route, $text ?? $user->getPseudo());
        }

        return 'Compte supprimé';
    }

    public function getUserProfileRole(User $user): ?string
    {
        foreach (self::ROLES as $k => $role) {
            if (in_array($k, $user->getRoles(), true)) {
                return $role;
            }
        }

        return null;
    }
}
