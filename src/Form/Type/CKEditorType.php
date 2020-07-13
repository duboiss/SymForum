<?php

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
        $attr = $view->vars['attr'];
        $class = isset($attr['class']) ? $attr['class'] . ' ' : '';
        $class .= 'editor';

        $attr['class'] = $class;
        $view->vars['attr'] = $attr;
        $view->vars['required'] = false;
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}
