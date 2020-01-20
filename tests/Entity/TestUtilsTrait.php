<?php

namespace App\Tests\Entity;

use Symfony\Component\Validator\ConstraintViolation;

trait TestUtilsTrait
{
    public function assertHasErrors($entity, int $number = 0)
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

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }
}