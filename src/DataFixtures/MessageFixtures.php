<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MessageFixtures extends BaseFixtures implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager): void
    {
        $this->createMany(Message::class, FixturesSettings::MESSAGES_COUNT, function (Message $message) {
            /** @var Thread $thread */
            $thread = $this->getRandomReference(Thread::class);

            $message->setAuthor($this->getRandomReference(User::class))
                ->setCreatedAt($this->faker->dateTimeBetween($thread->getCreatedAt()))
                ->setContent($this->faker->sentences(mt_rand(1, 15), true))
                ->setThread($thread);

            if ($this->faker->boolean) {
                $message->setUpdatedAt($this->faker->dateTimeBetween($message->getCreatedAt()));
                $message->setUpdatedBy($message->getAuthor());
            } else {
                $message->setUpdatedAt($message->getCreatedAt());
            }

            if ($thread->getLastMessage()->getCreatedAt() < $message->getCreatedAt()) {
                $thread->setLastMessage($message);
            }

            $forum = $thread->getForum();

            if (!$forum->getLastMessage() || $forum->getLastMessage()->getCreatedAt() < $message->getCreatedAt()) {
                $forum->setLastMessage($message);
            }

            $thread->incrementTotalMessages();
            $forum->incrementTotalMessages();
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ThreadFixtures::class,
        ];
    }
}
