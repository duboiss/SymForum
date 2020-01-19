<?php

namespace App\Tests\Entity;

use App\Entity\CoreOption;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoreOptionTest extends WebTestCase
{
    use FixturesTrait;

    public function getEntity(): CoreOption
    {
        return (new CoreOption())
            ->setName("CoreOption name")
            ->setValue("CoreOption value");
    }

    public function assertHasErrors(CoreOption $coreOption, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($coreOption);
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' : ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidBlankNameEntity()
    {
        $invalidCoreOption = $this->getEntity()->setName("");
        $this->assertHasErrors($invalidCoreOption, 1);
    }

    public function testInvalidUsedName()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/core_options.yaml']);
        $this->assertHasErrors($this->getEntity()->setName("max_online_users"), 1);
    }
}