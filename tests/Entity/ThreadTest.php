<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Forum;
use App\Entity\Thread;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThreadTest extends WebTestCase
{
    use FixturesTrait;
    use TestUtilsTrait;

    public function getEntity(): Thread
    {
        $forum = new Forum();

        return (new Thread())
            ->setTitle('Thread title')
            ->setForum($forum)
            ->setLock(false)
            ->setPin(false)
        ;
    }

    public function testInvalidBlankTitleEntity(): void
    {
        $invalidThread = $this->getEntity()->setTitle('');
        $this->assertHasErrors($invalidThread, 1);
    }

    public function testInvalidNullForumEntity(): void
    {
        $invalidThread = $this->getEntity()->setForum(null);
        $this->assertHasErrors($invalidThread, 1);
    }

    public function testInvalidUsedSlug(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/threads.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug('first-thread'), 1);
    }
}
