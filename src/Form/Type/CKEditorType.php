<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CKEditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars = array_merge($view->vars, [
            'attr' => [
                'class' => isset($view->vars['attr']['class']) ? $view->vars['attr']['class'] . ' editor' : 'editor',
            ],
            'required' => false,
        ]);
    }

    public function getParent(): ?string
    {
        return TextareaType::class;
    }
}
