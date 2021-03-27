<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class PageControllerTest extends AbstractControllerTest
{
    public function testIndexRedirection(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseRedirects('/forums/');
    }

    public function testDisplayMembers(): void
    {
        $this->responseIsSuccessful('/members');
        self::assertSelectorTextContains('h1', 'Liste des membres');
        self::assertSelectorExists('table');
    }

    public function testDisplayTeam(): void
    {
        $crawler = self::$client->request('GET', '/team');
        self::assertResponseIsSuccessful();

        self::assertSelectorTextContains('h1', 'Membres de l\'équipe');
        static::assertCount(2, $crawler->filter('table'));
        self::assertSelectorTextContains('h4', 'Administrateurs');
    }
}
