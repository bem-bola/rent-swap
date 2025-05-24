<?php

namespace App\Form;

use App\Service\Constances;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


class WarnMessageForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('action', ChoiceType::class, [
                'label' => 'Sélectionner une action',
                'multiple' => false,
                'expanded' => false,
                'choices'  => [
                    "Supprimer le message et envoyé un a avertissement à l'utilisateur" => null,
                    "Supprimer le message et bannir l'utilisateur" => Constances::BANNED,
                ],
            ])

            ->add('reason', TextType::class, [
                'label' => 'Motif',
                'required' => false,
            ])

        ;
    }

}
