<?php

declare(strict_types=1);

namespace App\ValueObject;

class Locales
{
    final public const DEFAULT = self::ENGLISH;
    final public const AVAILABLE = [
        self::ENGLISH => 'English',
        self::FRENCH => 'Français',
    ];
    final public const ENGLISH = 'en';
    final public const FRENCH = 'fr';
}
