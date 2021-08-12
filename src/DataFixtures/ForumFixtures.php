<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ForumFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Forum::class, FixturesSettings::FORUMS_COUNT, function (Forum $forum, $count): void {
            /** @var Category $category */
            $category = $this->getRandomReference(Category::class);

            $forum->setTitle($this->faker->words(4, true))
                ->setDescription($this->faker->sentence)
                ->setCategory($category)
                ->setParent(null)
                ->setPosition($count + 1)
            ;

            $this->faker->boolean(20) ? $forum->setLock(true) : $forum->setLock(false);
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
