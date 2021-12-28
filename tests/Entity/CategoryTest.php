<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Category;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    use TestUtilsTrait;

    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function getEntity(): Category
    {
        return (new Category())
            ->setTitle('Category name')
            ->setPosition(1)
        ;
    }

    public function testInvalidBlankTitleEntity(): void
    {
        $invalidCategory = $this->getEntity()->setTitle('');
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
        $this->databaseTool->loadAliceFixture([dirname(__DIR__) . '/Fixtures/categories.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug('first-category'), 1);
    }
}
