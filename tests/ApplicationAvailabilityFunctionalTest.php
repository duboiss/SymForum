<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\Controller\AbstractControllerTest;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAvailabilityFunctionalTest extends AbstractControllerTest
{
    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAliceFixture([__DIR__ . '/Fixtures/users.yaml']);
    }

    /**
     * @dataProvider urlPublicProvider
     */
    public function testPageIsSuccessful(string $url): void
    {
        $this->responseIsSuccessful($url);
    }

    /**
     * @dataProvider urlRestrictedAdminProvider
     * @dataProvider urlRestrictedModeratorProvider
     * @dataProvider urlRestrictedUserProvider
     */
    public function testRedirectToLogin(string $url): void
    {
        self::$client->request('GET', $url);
        self::assertResponseRedirects('/login');
    }

    /**
     * @dataProvider urlRestrictedUserProvider
     */
    public function testAuthenticatedUserAccess(string $url): void
    {
        $this->checkStatusUrl($url, 'demo', Response::HTTP_OK);
    }

    private function checkStatusUrl(string $url, string $username, int $expectedStatus): void
    {
        self::$client->loginUser($this->findUserByUsername($username));

        self::$client->request('GET', $url);
        self::assertResponseStatusCodeSame($expectedStatus);
    }

    /**
     * @dataProvider urlRestrictedAdminProvider
     */
    public function testAuthenticatedAdminAccess(string $url): void
    {
        $this->checkStatusUrl($url, 'admin', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'moderator', Response::HTTP_FORBIDDEN);
        $this->checkStatusUrl($url, 'demo', Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider urlRestrictedModeratorProvider
     */
    public function testAuthenticatedModeratorAccess(string $url): void
    {
        $this->checkStatusUrl($url, 'admin', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'moderator', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'demo', Response::HTTP_FORBIDDEN);
    }

    public function urlPublicProvider(): iterable
    {
        // Pages
        yield 'page_forums' => ['/forums/'];
        yield 'page_members' => ['/members'];
        yield 'page_team' => ['/team'];

        // Security
        yield 'security_login' => ['/login'];
    }

    public function urlRestrictedAdminProvider(): iterable
    {
        // Categories
        yield 'admin_categories' => ['/admin/categories/'];
        yield 'admin_categories_add' => ['/admin/categories/add'];

        // Forums
        yield 'admin_forums' => ['/admin/forums/'];
    }

    public function urlRestrictedModeratorProvider(): iterable
    {
        yield 'admin' => ['/admin'];

        // Reports
        yield 'admin_reports' => ['/admin/reports/'];

        // Users
        yield 'admin_users' => ['/admin/users/'];
    }

    public function urlRestrictedUserProvider(): iterable
    {
        // User profile
        yield 'user_profile' => ['/user/demo'];
        yield 'user_profile_messages' => ['/user/demo/messages'];
        yield 'user_profile_threads' => ['/user/demo/threads'];
    }
}
