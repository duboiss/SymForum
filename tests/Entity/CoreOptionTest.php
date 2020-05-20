<?php

namespace App\Tests\Entity;

use App\Entity\CoreOption;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreOptionTest extends WebTestCase
{
    use FixturesTrait, TestUtilsTrait;

    public function getEntity(): CoreOption
    {
        return (new CoreOption())
            ->setName("CoreOption name")
            ->setValue("CoreOption value");
    }

    public function testInvalidBlankNameEntity(): void
    {
        $invalidCoreOption = $this->getEntity()->setName("");
        $this->assertHasErrors($invalidCoreOption, 1);
    }

    public function testInvalidUsedName(): void
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/core_options.yaml']);
        $this->assertHasErrors($this->getEntity()->setName("max_online_users"), 1);
    }
}
