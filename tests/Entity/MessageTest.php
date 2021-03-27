<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Message;
use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageTest extends WebTestCase
{
    use TestUtilsTrait;

    public function getEntity(): Message
    {
        $thread = new Thread();

        return (new Message())
            ->setContent('Message content')
            ->setThread($thread)
        ;
    }

    public function testInvalidMinLengthContentEntity(): void
    {
        $invalidCategory = $this->getEntity()->setContent('a');
        $this->assertHasErrors($invalidCategory, 1);
    }
}
