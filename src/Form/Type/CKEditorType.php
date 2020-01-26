<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CKEditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'purify_html' => true,
            'label' => false,
            'attr' => [
                'class' => 'editor',
            ],
            'required' => false
        ]);
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}