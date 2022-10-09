<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('user_profile_link', [UserRuntime::class, 'getUserProfileLink'], ['is_safe' => ['html']]),
        ];
    }
}
