<?php

namespace App\Tests\Entity;

use App\Entity\Forum;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumTest extends WebTestCase
{
    use FixturesTrait, TestUtilsTrait;

    public function getEntity(): Forum
    {
        return (new Forum())
            ->setTitle("Forum title")
            ->setDescription("Forum description")
            ->setPosition(1);
    }

    public function testInvalidBlankTitleEntity()
    {
        $invalidCategory = $this->getEntity()->setTitle("");
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidBlankDescriptionEntity()
    {
        $invalidCategory = $this->getEntity()->setDescription("");
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidPositivePositionEntity()
    {
        $invalidCategory = $this->getEntity()->setPosition(-1);
        $this->assertHasErrors($invalidCategory, 1);

        $invalidCategory = $this->getEntity()->setPosition(0);
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidUsedSlug()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/forums.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug("forum-title"), 1);
    }
}