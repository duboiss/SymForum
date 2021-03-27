<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    protected function findUserByUsername(string $username): ?User
    {
        $userRepository = static::$container->get(UserRepository::class);

        return $userRepository->findOneBy(['pseudo' => $username]);
    }
}
