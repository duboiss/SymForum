<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;

class SecurityControllerTest extends AbstractControllerTest
{
    use FixturesTrait;

    public function testDisplayLogin(): void
    {
        $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'john@doe.com',
            'password' => 'password'
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects('/login');
        $this->client->followRedirect();

        self::assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithRightCredentials(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'email' => 'demo@demo.com',
            'password' => 'demo',
        ]);

        self::assertResponseRedirects('/forums');
    }
}
