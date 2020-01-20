<?php

namespace App\Tests\Entity;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageTest extends WebTestCase
{
    use TestUtilsTrait;

    public function getEntity(): Message
    {
        return (new Message())
            ->setContent("Message content");
    }

    public function testInvalidMinLengthContentEntity()
    {
        $invalidCategory = $this->getEntity()->setContent("a");
        $this->assertHasErrors($invalidCategory, 1);
    }
}