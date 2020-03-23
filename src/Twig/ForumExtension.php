<?php

namespace App\Twig;

use App\Entity\Category;
use App\Entity\Forum;
use InvalidArgumentException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ForumExtension extends AbstractExtension
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array|TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('breadcrumb', [$this, 'getBreadcrumbParts']),
        ];
    }

    /**
     * @param Category|Forum $forumOrCategory
     * @param array $parts
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

        $title = $forumOrCategory->getTitle();
        array_unshift($parts, ['url' => $url, 'title' => $title]);

        if ($forumOrCategory instanceof Forum && (($parent = $forumOrCategory->getParent()) || ($parent = $forumOrCategory->getCategory()))) {
            return $this->getBreadcrumbParts($parent, $parts);
        }

        return $parts;
    }
}