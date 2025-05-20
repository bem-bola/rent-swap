<?php

namespace App\Form;

use App\Service\Constances;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('action', ChoiceType::class, [
                'label' => 'Sélectionner une action',
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Supprimer' => Constances::DELETED,
                    'Bannir' => Constances::BANNED,
                ],
            ])
            ->add('reason', TextareaType::class, [
                'label' => 'Motif',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
