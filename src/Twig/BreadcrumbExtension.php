<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('breadcrumb', [BreadcrumbRuntime::class, 'getBreadcrumbParts']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('append_breadcrumb', [BreadcrumbRuntime::class, 'appendBreadcrumb']),
            new TwigFunction('get_breadcrumbs', [BreadcrumbRuntime::class, 'getBreadcrumbs']),
        ];
    }
}
