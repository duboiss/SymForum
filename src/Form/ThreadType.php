<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Message;
use App\Entity\Thread;
use App\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ThreadType extends AbstractType
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => $this->translator->trans('Thread title'),
                'attr' => [
                    'autofocus' => true,
                ],
                'constraints' => [
                    new NotBlank(['message' => $this->translator->trans('You must enter a title')]),
                    new Length([
                        'min' => Thread::TITLE_MIN_LENGTH,
                        'minMessage' => $this->translator->trans('The title must be at least {{ limit }} characters'),
                        'max' => Thread::TITLE_MAX_LENGTH,
                        'maxMessage' => $this->translator->trans('The title must not exceed {{ limit }} characters'),
                    ]),
                ],
            ])
            ->add('message', CKEditorType::class, [
                'constraints' => [
                    new NotBlank(['message' => $this->translator->trans('You must enter a message')]),
                    new Length([
                        'min' => Message::CONTENT_MIN_LENGTH,
                        'minMessage' => $this->translator->trans('The message must be at least 3 characters'),
                        'max' => Message::CONTENT_MAX_LENGTH,
                        'maxMessage' => $this->translator->trans('The message must not exceed {{ limit }} characters'),
                    ]),
                ],
            ])
        ;
    }
}
