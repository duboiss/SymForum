<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Forum;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class BreadcrumbRuntime implements RuntimeExtensionInterface
{
    private array $breadcrumbsPaths = [];

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function appendBreadcrumb(string $label, ?string $path = null): void
    {
        $pair = [$label, $path];
        if (!in_array($pair, $this->breadcrumbsPaths, true)) {
            $this->breadcrumbsPaths[] = $pair;
        }
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbsPaths;
    }

    public function getBreadcrumbParts(Category|Forum $item, array &$parts = []): array
    {
        if ($item instanceof Category) {
            $url = $this->urlGenerator->generate('category.show', ['slug' => $item->getSlug()]);
        } else {
            $url = $this->urlGenerator->generate('forum.show', ['slug' => $item->getSlug()]);
        }

        array_unshift($parts, ['url' => $url, 'name' => $item->getName()]);

        if ($item instanceof Forum && (($parent = $item->getParent()) || ($parent = $item->getCategory()))) {
            return $this->getBreadcrumbParts($parent, $parts);
        }

        return $parts;
    }
}
