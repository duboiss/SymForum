<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Twig\AppRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppRuntimeTest extends KernelTestCase
{
    private AppRuntime $runtime;

    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runtime = static::getContainer()->get(AppRuntime::class);
        $this->urlGenerator = static::getContainer()->get(UrlGeneratorInterface::class);
    }

    public function testLoginTargetPathWithRequest(): void
    {
        // static::getContainer()->get('request_stack')->push($this->client->getRequest());
        $path = '/login?redirect=/forums/threads/et-et-perferendis-sunt-vel-ut-numquam-eum';
        static::assertSame($this->urlGenerator->generate('security.login'), $this->runtime->loginTargetPath());
    }

    public function testLoginTargetPathWithoutRequest(): void
    {
        static::assertSame($this->urlGenerator->generate('security.login'), $this->runtime->loginTargetPath());
    }
}
