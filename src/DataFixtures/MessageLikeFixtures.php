<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\MessageLike;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MessageLikeFixtures extends BaseFixtures implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager): void
    {
        $this->createMany(MessageLike::class, FixturesSettings::MESSAGES_LIKES_COUNT, function (MessageLike $messageLike): void {
            /** @var Message $message */
            $message = $this->getRandomReference(Message::class);

            /** @var User $user */
            $user = $this->getRandomReference(User::class);

            $messageLike->setMessage($message)
                ->setUser($user)
            ;
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MessageFixtures::class,
        ];
    }
}
