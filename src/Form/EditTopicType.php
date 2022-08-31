<?php

namespace App\Form;

use App\Entity\Topics;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EditTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Titre'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 255)
                ]
            ])
            ->add('subTitle', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Sous titre'
                ],
                'constraints' => [
                    new Assert\Length(min: 3, max: 128)
                ]
            ])
            ->add('mainContent', CKEditorType::class, [
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topics::class,
        ]);
    }
}
