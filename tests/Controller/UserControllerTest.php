<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserControllerTest extends AbstractControllerTest
{
    use FixturesTrait;

    private function logAsDemo(): void
    {
        $users = $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);
        $this->logIn($users['user_demo']);
    }

    public function testDisplayProfile(): void
    {
        $this->logAsDemo();
        $this->responseIsSuccessful('/user/admin');
        $this->responseIsSuccessful('/user/demo');

        self::assertSelectorTextContains('h3', 'demo');
        self::assertSelectorTextContains('p', 'Date d\'inscription');
        self::assertSelectorTextContains('p', 'Dernière activité');
        self::assertSelectorTextContains('p', 'Nombre de messages');
        self::assertSelectorTextContains('h4', 'Derniers sujets créés');
    }

    public function testDisplayUserThreads(): void
    {
        $this->logAsDemo();
        $this->responseIsSuccessful('/user/demo/threads');
        self::assertSelectorTextContains('h3', 'demo');
    }

    public function testDisplayUserMessages(): void
    {
        $this->logAsDemo();
        $this->responseIsSuccessful('/user/demo/messages');
        self::assertSelectorTextContains('h3', 'demo');
    }
}
