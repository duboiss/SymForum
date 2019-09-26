<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
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

    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var array Array of users
     */
    private $users = [];

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadForums($manager);
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        $demoUser = new User();
        $demoUser->setPseudo('demo')
            ->setHash($this->encoder->encodePassword($demoUser, 'demo'))
            ->setEmail('demo@demo.com')
            ->setRegistrationIp('127.0.0.1');

        $manager->persist($demoUser);

        for ($u = 1; $u <= 25; $u++) {
            $user = new User();
            $user->setPseudo($this->faker->userName)
                ->setHash($this->encoder->encodePassword($user, 'password'))
                ->setEmail($this->faker->email)
                ->setRegistrationIp('127.0.0.1');

            $manager->persist($user);
            $this->users[] = $user;
        }
    }

    public function loadForums(ObjectManager $manager)
    {
        for ($c = 1; $c <= 3; $c++) {
            $category = new Category();
            $category->setTitle($this->faker->words(4, true))
                ->setPosition($c);

            $manager->persist($category);

            for ($f = 1; $f <= mt_rand(2, 5); $f++) {
                $forum = new Forum();
                $forum->setTitle($this->faker->words(4, true))
                    ->setDescription($this->faker->sentence)
                    ->setCategory($category)
                    ->setParent(NULL)
                    ->setPosition($f);

                $manager->persist($forum);

                for ($tf = 1; $tf <= mt_rand(10, 20); $tf++) {
                    $this->makeThread($forum, $manager);
                }

                // Sub-forums
                if ($this->faker->boolean) {
                    for ($sf = 1; $sf <= mt_rand(1, 3); $sf++) {
                        $subForum = new Forum();
                        $subForum->setTitle($this->faker->words(4, true))
                            ->setDescription($this->faker->sentence)
                            ->setCategory(NULL)
                            ->setParent($forum)
                            ->setPosition($sf);

                        $manager->persist($subForum);

                        for ($tsb = 1; $tsb <= mt_rand(10, 20); $tsb++) {
                            $this->makeThread($subForum, $manager);
                        }
                    }
                }
            }
        }
    }

    private function makeThread(Forum $forum, ObjectManager $manager)
    {
        $thread = new Thread();
        $thread->setTitle($this->faker->words(rand(4, 8), true))
            ->setAuthor($this->getRandomUser())
            ->setCreatedAt($this->faker->dateTimeBetween('-1 years'))
            ->setForum($forum);

        if ($this->faker->boolean(40)) $thread->setLocked(true);

        for ($m = 0; $m <= mt_rand(1, 60); $m++) {
            if ($m === 0) {
                $this->makeMessage($thread, $manager, true);
            } else {
                $this->makeMessage($thread, $manager);
            }
        }

        $manager->persist($thread);
    }

    private function makeMessage(Thread $thread, ObjectManager $manager, bool $firstMessage = false)
    {
        $message = new Message();

        if ($firstMessage) {
            $message->setAuthor($thread->getAuthor())
                ->setPublishedAt($thread->getCreatedAt());
        } else {
            $message->setAuthor($this->getRandomUser())
                ->setPublishedAt($this->faker->dateTimeBetween($thread->getCreatedAt()));
        }

        $message->setContent($this->faker->sentences(mt_rand(1, 15), true))
            ->setThread($thread);

        if($this->faker->boolean()) {
            $message->setUpdatedAt($this->faker->dateTimeBetween($message->getPublishedAt()));
        } else $message->setUpdatedAt(null);

        $manager->persist($message);
    }

    private function getRandomUser(): User
    {
        return $this->users[rand(0, count($this->users) - 1)];
    }
}
