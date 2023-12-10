<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\ValueObject\Locales;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    protected function loadData(ObjectManager $manager): void
    {
        $demoUser = new User();
        $demoUser->setPseudo('demo')
            ->setHash($this->hasher->hashPassword($demoUser, 'demo'))
            ->setEmail('demo@demo.com')
        ;

        $demofrUser = new User();
        $demofrUser->setPseudo('demofr')
            ->setHash($this->hasher->hashPassword($demoUser, 'demo'))
            ->setEmail('demofr@demo.com')
            ->setLocale(Locales::FRENCH)
        ;

        $adminUser = new User();
        $adminUser->setPseudo('admin')
            ->setHash($this->hasher->hashPassword($demoUser, 'admin'))
            ->setEmail('admin@admin.com')
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($demoUser);
        $manager->persist($demofrUser);
        $manager->persist($adminUser);

        $this->createMany(User::class, FixturesSettings::USERS_COUNT, function (User $user): void {
            $user->setPseudo($this->faker->userName)
                ->setHash($this->hasher->hashPassword($user, 'password'))
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
