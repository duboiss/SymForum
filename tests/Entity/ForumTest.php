<?php

namespace App\Tests\Entity;

use App\Entity\Forum;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ForumTest extends WebTestCase
{
    use FixturesTrait;

    public function getEntity(): Forum
    {
        return (new Forum())
            ->setTitle("Forum title")
            ->setDescription("Forum description")
            ->setPosition(1);
    }

    public function assertHasErrors(Forum $forum, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($forum);
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' : ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
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