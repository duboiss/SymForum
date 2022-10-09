<?php

declare(strict_types=1);

namespace App\Tests\Twig;

use App\Entity\User;
use App\Twig\UserRuntime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRuntimeTest extends KernelTestCase
{
    private UserRuntime $runtime;

    private UrlGeneratorInterface $urlGenerator;

    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->runtime = static::getContainer()->get(UserRuntime::class);
        $this->urlGenerator = static::getContainer()->get(UrlGeneratorInterface::class);
        $this->translator = static::getContainer()->get(TranslatorInterface::class);
    }

    public function testGetUserProfileLinkWithoutUser(): void
    {
        static::assertSame($this->translator->trans('user.deleted'), $this->runtime->getUserProfileLink(null));
    }

    public function testGetUserProfileLinkWithUserAndWithoutText(): void
    {
        $user = (new User())
            ->setPseudo('foo')
        ;
        $route = $this->urlGenerator->generate('user.profile', ['slug' => $user->getSlug()]);
    }

    public function testGetUserProfileLinkWithUserAndText(): void
    {
    }
}
