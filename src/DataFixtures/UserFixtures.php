<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixtures
{
    public function __construct(private UserPasswordEncoderInterface $encoder)
    {
    }

    protected function loadData(ObjectManager $manager): void
    {
        $demoUser = new User();
        $demoUser->setPseudo('demo')
            ->setHash($this->encoder->encodePassword($demoUser, 'demo'))
            ->setEmail('demo@demo.com')
        ;

        $adminUser = new User();
        $adminUser->setPseudo('admin')
            ->setHash($this->encoder->encodePassword($demoUser, 'admin'))
            ->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($demoUser);
        $manager->persist($adminUser);

        $this->createMany(User::class, FixturesSettings::USERS_COUNT, function (User $user): void {
            $user->setPseudo($this->faker->userName)
                ->setHash($this->encoder->encodePassword($user, 'password'))
                ->setEmail($this->faker->email)
                ->setCreatedAt($this->faker->dateTimeBetween('-1 years'))
                ->setLastActivityAt($this->faker->dateTimeBetween($user->getCreatedAt()))
            ;

            if ($this->faker->boolean(7)) {
                $user->setRoles(['ROLE_MODERATOR']);
            }
        });

        $manager->flush();
    }
}
