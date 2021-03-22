<?php

namespace App\Service;

use App\Entity\CoreOption;
use App\Repository\CoreOptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class OptionService
{
    /** @var CoreOption[] */
    private array $cache = [];

    public function __construct(private EntityManagerInterface $em, private CoreOptionRepository $coreOptionRepository)
    {
    }

    public function get(string $optionName, string $default = null): ?string
    {
        return ($option = $this->getEntity($optionName)) ? $option->getValue() : $default;
    }

    public function set(string $optionName, string $value, bool $flush = true): void
    {
        $option = $this->getEntity($optionName);

        if (!$option) {
            $option = new CoreOption();
            $option->setName($optionName);
        }

        $option->setValue($value);
        $this->em->persist($option);

        if ($flush) {
            $this->em->flush();
        }
    }

    private function getEntity(string $optionName): ?CoreOption
    {
        if (!isset($this->cache[$optionName])) {
            if ($option = $this->coreOptionRepository->findCoreOptionByName($optionName)) {
                $this->cache[$optionName] = $option;
            } else {
                return null;
            }
        }

        return $this->cache[$optionName];
    }
}
