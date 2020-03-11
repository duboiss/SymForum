<?php

namespace App\Tests;

use App\Entity\User;
use App\Tests\Controller\NeedLoginTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    use FixturesTrait, NeedLoginTrait;

    /** @var KernelBrowser */
    private $client;

    /** @var User[] */
    private $users = [];

    protected function setUp(): void
    {
        $this->users = $this->loadFixtureFiles([__DIR__ . '/Fixtures/users.yaml']);
        $this->client = static::createClient();
    }

    /**
     * @dataProvider urlPublicProvider
     * @param string $url
     */
    public function testPageIsSuccessful(string $url)
    {
        $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider urlRestrictedAdminProvider
     * @dataProvider urlRestrictedModeratorProvider
     * @dataProvider urlRestrictedUserProvider
     * @param string $url
     */
    public function testRedirectToLogin(string $url)
    {
        $this->client->request('GET', $url);
        $this->assertResponseRedirects('/login');
    }

    /**
     * @dataProvider urlRestrictedUserProvider
     * @param string $url
     */
    public function testAuthenticatedUserAccess(string $url)
    {
        $this->checkStatusUrl($url, 'user_demo', Response::HTTP_OK);
    }

    /**
     * @param string $url
     * @param string $username
     * @param int $expectedStatus
     */
    private function checkStatusUrl(string $url, string $username, int $expectedStatus)
    {
        $this->logIn($this->client, $this->users[$username]);
        $this->client->request('GET', $url);
        $this->assertResponseStatusCodeSame($expectedStatus);
    }

    /**
     * @dataProvider urlRestrictedAdminProvider
     * @param string $url
     */
    public function testAuthenticatedAdminAccess(string $url)
    {
        $this->checkStatusUrl($url, 'user_admin', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'user_moderator', Response::HTTP_FORBIDDEN);
        $this->checkStatusUrl($url, 'user_demo', Response::HTTP_FORBIDDEN);
    }

    /**
     * @dataProvider urlRestrictedModeratorProvider
     * @param string $url
     */
    public function testAuthenticatedModeratorAccess(string $url)
    {
        $this->checkStatusUrl($url, 'user_admin', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'user_moderator', Response::HTTP_OK);
        $this->checkStatusUrl($url, 'user_demo', Response::HTTP_FORBIDDEN);
    }

    public function urlPublicProvider()
    {
        // Pages
        yield 'page_forums' => ['/forums'];
        yield 'page_members' => ['/members'];
        yield 'page_team' => ['/team'];

        // Security
        yield 'security_login' => ['/login'];
    }

    public function urlRestrictedAdminProvider()
    {
        // Categories
        yield 'panel_categories' => ['/panel/categories'];
        yield 'panel_categories_add' => ['/panel/categories/add'];

        // Forums
        yield 'panel_forums' => ['/panel/forums'];
    }

    public function urlRestrictedModeratorProvider()
    {
        yield 'panel' => ['/panel'];

        // Reports
        yield 'panel_reports' => ['/panel/reports'];

        // Users
        yield 'panel_users' => ['/panel/users'];
    }

    public function urlRestrictedUserProvider()
    {
        // User profile
        yield 'user_profile' => ['/user/demo'];
        yield 'user_profile_messages' => ['/user/demo/messages'];
        yield 'user_profile_threads' => ['/user/demo/threads'];
    }
}