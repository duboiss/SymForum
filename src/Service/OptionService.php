<?php

namespace App\Service;

use App\Entity\CoreOption;
use App\Repository\CoreOptionRepository;
use Doctrine\ORM\EntityManagerInterface;

class OptionService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CoreOptionRepository */
    private $coreOptionRepository;

    /** @var array */
    private $cache = [];

    public function __construct(EntityManagerInterface $em, CoreOptionRepository $coreOptionRepository)
    {
        $this->em = $em;
        $this->coreOptionRepository = $coreOptionRepository;
    }


    /**
     * @param string $optionName
     * @param string $default
     * @return CoreOption|string
     */
    public function get(string $optionName, ?string $default = null): ?string
    {
        return ($option = $this->getEntity($optionName)) ? $option->getValue() : $default;
    }

    /**
     * @param string $optionName
     * @param string $value
     * @param bool $flush
     */
    public function set(string $optionName, string $value, bool $flush = true): void
    {
        $option = $this->getEntity($optionName);

        if(!$option) {
            $option = new CoreOption();
            $option->setName($optionName);
        }

        $option->setValue($value);

        $this->em->persist($option);

        if($flush) {
            $this->em->flush();
        }
    }

    /**
     * @param string $optionName
     * @return CoreOption|null
     */
    private function getEntity(string $optionName): ?CoreOption
    {
        if(!isset($this->cache[$optionName])) {
            $this->cache[$optionName] = $this->coreOptionRepository->findOneBy(['name' => $optionName]);
        }

        return $this->cache[$optionName];
    }
}
