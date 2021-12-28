<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class UserControllerTest extends AbstractControllerTest
{
    private function logAsDemo(): void
    {
        self::$client->loginUser($this->findUserByUsername('demo'));
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
