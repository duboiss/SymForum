<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractControllerTest extends WebTestCase
{
    protected static KernelBrowser $client;

    protected function setUp(): void
    {
        self::$client = static::createClient();
    }

    protected function responseIsSuccessful(string $url): void
    {
        self::$client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    protected function logIn(User $user): void
    {
        $session = self::$client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        self::$client->getCookieJar()->set($cookie);
    }
}
