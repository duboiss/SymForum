<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    use FixturesTrait;

    public function getEntity(): Category
    {
        return (new Category())
            ->setTitle("Category name")
            ->setPosition(1);
    }

    public function assertHasErrors(Category $category, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($category);
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

    public function testInvalidBlankTitleEntity()
    {
        $invalidCategory = $this->getEntity()->setTitle("");
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidPositivePositionEntity()
    {
        $invalidCategory = $this->getEntity()->setPosition(-1);
        $this->assertHasErrors($invalidCategory, 1);

        $invalidCategory = $this->getEntity()->setPosition(0);
        $this->assertHasErrors($invalidCategory, 1);
    }

    public function testInvalidUsedSlug()
    {
        $this->loadFixtureFiles([dirname(__DIR__) . '/Fixtures/categories.yaml']);
        $this->assertHasErrors($this->getEntity()->setSlug("first-category"), 1);
    }
}