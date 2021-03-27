<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use Symfony\Component\Validator\ConstraintViolation;

trait TestUtilsTrait
{
    public function assertHasErrors(object $entity, int $number = 0): void
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($entity);
        $messages = [];

        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' : ' . $error->getMessage();
        }

        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
}
