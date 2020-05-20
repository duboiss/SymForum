<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class PageControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private Crawler $crawler;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function responseIsSuccessful(string $url): void
    {
        $this->crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    public function testIndexRedirection(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/forums');
    }

    public function testDisplayForums(): void
    {
        $this->responseIsSuccessful('/forums');
    }

    public function testDisplayMembers(): void
    {
        $this->responseIsSuccessful('/members');
        $this->assertSelectorTextContains('h1', 'Liste des membres');
        $this->assertSelectorExists('table');
    }

    public function testDisplayTeam(): void
    {
        $this->responseIsSuccessful('/team');
        $this->assertSelectorTextContains('html h1', 'Membres de l\'équipe');
        $this->assertEquals(2, $this->crawler->filter('html table')->count());
        $this->assertSelectorTextContains('html h4', 'Administrateurs');
    }
}
