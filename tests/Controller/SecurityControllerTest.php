<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;

class SecurityControllerTest extends AbstractControllerTest
{
    use FixturesTrait;

    public function testDisplayLogin(): void
    {
        self::$client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);
        $crawler = self::$client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'john@doe.com',
            'password' => 'password',
        ]);

        self::$client->submit($form);
        self::assertResponseRedirects('/login');
        self::$client->followRedirect();

        self::assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithRightCredentials(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);

        $csrfToken = self::$client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        self::$client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'email' => 'demo@demo.com',
            'password' => 'demo',
        ]);

        self::assertResponseRedirects('/forums/');
    }
}
