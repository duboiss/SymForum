<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Forum;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class BreadcrumbExtension extends AbstractExtension
{
    private array $breadcrumbsPaths = [];

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('breadcrumb', [$this, 'getBreadcrumbParts']),
        ];
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('append_breadcrumb', [$this, 'appendBreadcrumb']),
            new TwigFunction('get_breadcrumbs', [$this, 'getBreadcrumbs']),
        ];
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

    /**
     * @return string[]
     */
    public function getBreadcrumbParts(Category|Forum $item, array &$parts = []): array
    {
        if ($item instanceof Category) {
            $url = $this->urlGenerator->generate('category.show', ['slug' => $item->getSlug()]);
        } elseif ($item instanceof Forum) {
            $url = $this->urlGenerator->generate('forum.show', ['slug' => $item->getSlug()]);
        } else {
            throw new InvalidArgumentException('Filtered object must be an instance of Forum or Category.');
        }

        array_unshift($parts, ['url' => $url, 'title' => $item->getTitle()]);

        if ($item instanceof Forum && (($parent = $item->getParent()) || ($parent = $item->getCategory()))) {
            return $this->getBreadcrumbParts($parent, $parts);
        }

        return $parts;
    }
}
