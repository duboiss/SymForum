<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\UserExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class UserExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $functions = (new UserExtension())->getFunctions();
        static::assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }
}
