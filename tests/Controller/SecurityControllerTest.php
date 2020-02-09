<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    /** @var KernelBrowser|null  */
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    public function testDisplayLogin()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Connexion')->form([
            'email' => 'john@doe.com',
            'password' => 'password'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();

        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithRightCredentials()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/users.yaml']);

        $csrfToken = $this->client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $this->client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'email' => 'demo@demo.com',
            'password' => 'demo',
        ]);

        $this->assertResponseRedirects('/forums');
    }
}