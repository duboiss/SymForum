<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Category::class, 3, function (Category $category, $count) {
            $category->setTitle($this->faker->words(4, true))
                ->setPosition($count);
        });

        $manager->flush();
    }
}
