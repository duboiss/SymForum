<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\BreadcrumbExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BreadcrumbExtensionTest extends TestCase
{
    public function testGetFilters(): void
    {
        $filters = (new BreadcrumbExtension())->getFilters();
        static::assertContainsOnlyInstancesOf(TwigFilter::class, $filters);
    }

    public function testGetFunctions(): void
    {
        $functions = (new BreadcrumbExtension())->getFunctions();
        static::assertContainsOnlyInstancesOf(TwigFunction::class, $functions);
    }
}
