<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Forum;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumTest extends WebTestCase
{
    use TestUtilsTrait;

    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function getEntity(): Forum
    {
        return (new Forum())
            ->setTitle('Forum title')
            ->setDescription('Forum description')
            ->setPosition(1)
        ;
    }

    public function testInvalidBlankTitleEntity(): void
    {
        $invalidForum = $this->getEntity()->setTitle('');
        $this->assertHasErrors($invalidForum, 1);
    }

    public function testInvalidPositivePositionEntity(): void
    {
        $invalidForum = $this->getEntity()->setPosition(-1);
        $this->assertHasErrors($invalidForum, 1);

        $invalidForum = $this->getEntity()->setPosition(0);
        $this->assertHasErrors($invalidForum, 1);
    }

    public function testInvalidUsedSlug(): void
    {
        $this->databaseTool->loadAliceFixture([dirname(__DIR__) . '/Fixtures/forums.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug('forum-title'), 1);
    }
}
