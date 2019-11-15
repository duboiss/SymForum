<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ForumFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(Forum::class, 10, function (Forum $forum, $count) {
            $forum->setTitle($this->faker->words(4, true))
                ->setDescription($this->faker->sentence)
                ->setCategory($this->getRandomReference(Category::class))
                ->setParent(NULL)
                ->setPosition($count);

            $this->faker->boolean(20) ? $forum->setLocked(true) : $forum->setLocked(false);
        });

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
