<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du sujet',
                'attr' => [
                    'autofocus' => true,
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez saisir un titre']),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Le titre doit faire au moins {{ limit }} caractères.',
                        'max' => 50,
                        'maxMessage' => 'Le titre doit faire au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('message', CKEditorType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Vous devez saisir un message']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre message doit faire au moins 3 caractères.',
                        'max' => 6000,
                        'maxMessage' => 'Votre message doit faire au maximum {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }
}
