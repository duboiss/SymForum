<?php

namespace App\Tests\Entity;

use App\Entity\Forum;
use App\Entity\Thread;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThreadTest extends WebTestCase
{
    use FixturesTrait, TestUtilsTrait;

    public function getEntity(): Thread
    {
        $forum = new Forum();

        return (new Thread())
            ->setTitle("Thread title")
            ->setForum($forum)
            ->setIsLock(false)
            ->setIsPin(false);
    }

    public function testInvalidBlankTitleEntity()
    {
        $invalidThread = $this->getEntity()->setTitle("");
        $this->assertHasErrors($invalidThread, 1);
    }

    public function testInvalidNullForumEntity()
    {
        $invalidThread = $this->getEntity()->setForum(null);
        $this->assertHasErrors($invalidThread, 1);
    }


    public function testInvalidUsedSlug()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/threads.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug("first-thread"), 1);
    }
}