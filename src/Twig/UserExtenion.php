<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtenion extends AbstractExtension
{
    const ROLES = [
        'ROLE_ADMIN' => 'Administrateur',
        'ROLE_MODERATOR' => 'Modérateur'
    ];

    // TODO Store a color for each role

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_profile_link', [$this, 'userProfileLink'], ['is_safe' => ['html']]),
            new TwigFunction('user_profile_role', [$this, 'userProfileRole'])
        ];
    }

    /**
     * @param User $user
     * @param string|null $text
     * @param string|null $class
     * @return string
     */
    public function userProfileLink(User $user, string $text = null, string $class = null): string
    {
        $route = $this->generator->generate('user.profile', ['slug' => $user->getSlug()]);
        $classAttr = $class ? ' class="' . $class . '"' : '';

        // TODO Return with color role in class
        return sprintf('<a href="%s"' . $classAttr . '>%s</a>', $route, $text ?? $user->getPseudo());
    }

    /**
     * @param User $user
     * @return string|null
     */
    public function userProfileRole(User $user): ?string
    {
        $roles = $user->getRoles();

        foreach (self::ROLES as $k => $role) {
            if (in_array($k, $roles)) return $role;
        }

        return null;
    }
}