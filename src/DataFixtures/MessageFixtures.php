<?php

namespace App\DataFixtures;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class MessageFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Message::class, 2500, function (Message $message) {
            /** @var Thread $thread */
            $thread = $this->getRandomReference(Thread::class);

            $message->setAuthor($this->getRandomReference(User::class))
                ->setPublishedAt($this->faker->dateTimeBetween($thread->getCreatedAt()))
                ->setContent($this->faker->sentences(mt_rand(1, 15), true))
                ->setThread($thread);

            $this->faker->boolean() ? $message->setUpdatedAt($this->faker->dateTimeBetween($message->getPublishedAt())) : $message->setUpdatedAt(null);

            if ($thread->getLastMessage()->getPublishedAt() < $message->getPublishedAt()) $thread->setLastMessage($message);

            /** @var Forum $forum */
            $forum = $thread->getForum();

            if (!$forum->getLastMessage() || $forum->getLastMessage()->getPublishedAt() < $message->getPublishedAt()) $forum->setLastMessage($message);

            $thread->incrementTotalMessages();
            $forum->incrementTotalMessages();
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
            ThreadFixtures::class
        ];
    }
}
