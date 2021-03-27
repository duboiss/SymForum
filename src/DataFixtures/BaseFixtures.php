<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;

abstract class BaseFixtures extends Fixture
{
    protected Generator $faker;

    private ObjectManager $manager;

    private array $referencesIndex = [];

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();

        $this->loadData($manager);
    }

    abstract protected function loadData(ObjectManager $manager): void;

    protected function createMany(string $className, int $count, callable $factory): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);
            $this->addReference($className . '_' . $i, $entity);
        }
    }

    /**
     * @throws Exception
     */
    protected function getRandomReference(string $className): object
    {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (str_starts_with($key, $className . '_')) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className])) {
            throw new Exception(sprintf('Cannot find any references for class "%s"', $className));
        }

        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);

        return $this->getReference($randomReferenceKey);
    }

    /**
     * @throws Exception
     */
    protected function getRandomReferences(string $className, int $count): array
    {
        $references = [];

        while (count($references) < $count) {
            $references[] = $this->getRandomReference($className);
        }

        return $references;
    }
}
