<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    use FixturesTrait, TestUtilsTrait;

    public function getEntity(): Category
    {
        return (new Category())
            ->setTitle("Category name")
            ->setPosition(1);
    }

    public function testInvalidBlankTitleEntity(): void
    {
        $invalidCategory = $this->getEntity()->setTitle("");
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidPositivePositionEntity(): void
    {
        $invalidCategory = $this->getEntity()->setPosition(-1);
        $this->assertHasErrors($invalidCategory, 1);

        $invalidCategory = $this->getEntity()->setPosition(0);
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidUsedSlug(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/categories.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug("first-category"), 1);
    }
}
