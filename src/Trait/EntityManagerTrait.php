<?php

declare(strict_types=1);

namespace App\Trait;

use Doctrine\ORM\EntityManagerInterface;

trait EntityManagerTrait
{
    public function getChangeSet(EntityManagerInterface $em, object $entity): array {
        $uow = $em->getUnitOfWork();
        $uow->computeChangeSets();

        return $uow->getEntityChangeSet($entity);
    }
}
