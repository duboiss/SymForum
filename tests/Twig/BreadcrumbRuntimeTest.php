<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Entity\Category;
use App\Entity\Forum;
use App\Twig\BreadcrumbRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BreadcrumbRuntimeTest extends KernelTestCase
{
    private BreadcrumbRuntime $runtime;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runtime = static::getContainer()->get(BreadcrumbRuntime::class);
    }

    public function testAppendBreadcrumb(): void
    {
        static::assertCount(0, $this->runtime->getBreadcrumbs());
        $this->runtime->appendBreadcrumb('Home', '/');
        static::assertCount(1, $this->runtime->getBreadcrumbs());
        $this->runtime->appendBreadcrumb('About', '/about');
        static::assertCount(2, $this->runtime->getBreadcrumbs());
    }

    public function testAppendBreadcrumbDoesntCreateDuplicates(): void
    {
        $this->runtime->appendBreadcrumb('Home', '/');
        static::assertCount(1, $this->runtime->getBreadcrumbs());

        $this->runtime->appendBreadcrumb('Home', '/');
        static::assertCount(1, $this->runtime->getBreadcrumbs());
    }

    public function testGetBreadcrumbs(): void
    {
        static::assertIsArray($this->runtime->getBreadcrumbs());
    }

    public function testGetCategoryBreadcrumbParts(): void
    {
        $category = (new Category())
            ->setName('Lorem')
            ->setSlug('lorem')
        ;

        $categoryParts = $this->runtime->getBreadcrumbParts($category);
        static::assertIsArray($categoryParts);
        static::assertCount(1, $categoryParts);
        static::assertSame(['url', 'name'], array_keys($categoryParts[0]));
    }

    public function testGetBreadcrumbParts(): void
    {
        $category = (new Category())
            ->setName('Lorem')
            ->setSlug('lorem')
        ;

        $forum = (new Forum())
            ->setName('Ipsum')
            ->setSlug('ipsum')
            ->setCategory($category)
        ;

        $forumParts = $this->runtime->getBreadcrumbParts($forum);
        static::assertIsArray($forumParts);
        static::assertCount(2, $forumParts);
        static::assertSame(['url', 'name'], array_keys($forumParts[0]));
        static::assertSame(['url', 'name'], array_keys($forumParts[1]));
    }
}
