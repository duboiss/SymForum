<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Category::class, FixturesSettings::CATEGORIES_COUNT, function (Category $category, $count): void {
            $category->setName($this->faker->words(4, true))
                ->setPosition($count + 1)
            ;
        });

        $manager->flush();
    }
}
