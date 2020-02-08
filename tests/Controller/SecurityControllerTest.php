<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testDisplayLogin()
    {
        $client = self::createClient();
        $client->request('GET', '/login');
        $client->request('GET', Response::HTTP_OK);
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'john@doe.com',
            'password' => 'password'
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithRightCredentials()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);
        $client = self::createClient();

        $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'email' => 'demo@demo.com',
            'password' => 'demo',
        ]);

        $this->assertResponseRedirects('/forums');
    }
}