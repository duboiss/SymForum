<?php

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
    private string $activeLabel = '';

    private array $breadcrumbsPaths = [];

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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
            new TwigFunction('get_active_label', [$this, 'getActiveLabel']),
            new TwigFunction('set_active_label', [$this, 'setActiveLabel']),
            new TwigFunction('get_breadcrumbs', [$this, 'getBreadcrumbs']),
            new TwigFunction('append_breadcrumb', [$this, 'appendBreadcrumb']),
        ];
    }

    /**
     * @param Category|Forum $forumOrCategory
     *
     * @return string[]
     */
    public function getBreadcrumbParts($forumOrCategory, array &$parts = []): array
    {
        if ($forumOrCategory instanceof Category) {
            $url = $this->urlGenerator->generate('category.show', ['slug' => $forumOrCategory->getSlug()]);
        } elseif ($forumOrCategory instanceof Forum) {
            $url = $this->urlGenerator->generate('forum.show', ['slug' => $forumOrCategory->getSlug()]);
        } else {
            throw new InvalidArgumentException('Filtered object must be an instance of Forum or Category.');
        }

        array_unshift($parts, ['url' => $url, 'title' => $forumOrCategory->getTitle()]);

        if ($forumOrCategory instanceof Forum && (($parent = $forumOrCategory->getParent()) || ($parent = $forumOrCategory->getCategory()))) {
            return $this->getBreadcrumbParts($parent, $parts);
        }

        return $parts;
    }

    public function getActiveLabel(): string
    {
        return $this->activeLabel;
    }

    public function setActiveLabel(string $label): void
    {
        $this->activeLabel = $label;
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbsPaths;
    }

    public function appendBreadcrumb(string $path, string $label): void
    {
        $pair = [$path, $label];
        if (!in_array($pair, $this->breadcrumbsPaths, true)) {
            $this->breadcrumbsPaths[] = $pair;
        }
    }
}
