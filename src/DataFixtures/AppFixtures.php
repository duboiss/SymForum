<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($u = 1; $u <= 25; $u++) {
            $user = new User();
            $user->setPseudo($faker->userName)
                 ->setHash($this->encoder->encodePassword($user, 'password'))
                 ->setEmail($faker->email);
            $manager->persist($user);
        }

        for($c = 1; $c <= 3; $c ++) {
            $category = new Category();
            $category->setTitle($faker->words(4, true))
                     ->setPosition($c);

            $manager->persist($category);

            for($f = 1; $f <= mt_rand(2, 5); $f++) {
                $forum = new Forum();
                $forum->setTitle($faker->words(4, true))
                      ->setDescription($faker->sentence)
                      ->setCategory($category)
                      ->setParent(NULL);

                $manager->persist($forum);

                // Sub-forums
                if($faker->boolean) {
                    for($sf =1; $sf <= mt_rand(1,3); $sf++) {
                        $subForum = new Forum();
                        $subForum->setTitle($faker->words(4, true))
                            ->setDescription($faker->sentence)
                            ->setCategory(NULL)
                            ->setParent($forum);

                        $manager->persist($subForum);
                    }
                }
            }
        }

        $manager->flush();
    }
}
