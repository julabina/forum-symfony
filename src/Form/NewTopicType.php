<?php

namespace App\Form;

use App\Entity\Topics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Validator\Constraints as Assert;

class NewTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre*',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 255)
                ]
            ])
            ->add('subTitle', TextType::class, [
                'label' => 'Sous-titre',
                'required' => false,
                'constraints' => [
                    new Assert\Length(min: 3, max: 128)
                ]
            ])
            ->add('mainContent', CKEditorType::class, [
                'label' => 'Contenu*'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'OK'
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
