<?php

namespace App\Form;

use App\Service\Constances;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionDeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Sélectionner une action',
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    'Valider' => Constances::VALIDED,
                    'Rejeter' => Constances::REJECTED,
                    'Supprimer' => Constances::DELETED,
                ],
            ])
            ->add('reason', TextType::class, [
                'label' => 'Motif',
                'required' => false
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false
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
