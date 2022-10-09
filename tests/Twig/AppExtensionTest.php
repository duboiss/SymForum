<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\AppExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class AppExtensionTest extends TestCase
{
    public function testGetFunctions(): void
    {
        $functions = (new AppExtension())->getFunctions();
        static::assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }
}
