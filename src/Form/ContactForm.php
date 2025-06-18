<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ est obligatoire',
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-zÀ-ÖØ-öø-ÿ]{2,}$/',
                        'message' => 'Le nom doit contenir au moins 2 caractères alphabétiques.',
                    ]),
                ],
                'attr' => [
                    'data-type-pattern' => "name"
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une adresse e-mail.',
                    ]),
                    new Email([
                        'message' => 'Veuillez entrer une adresse e-mail valide.',
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Za-z0-9._%+-]{2,}@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                        'message' => 'Adresse email invalid',
                    ]),
                ],
                'attr' => [
                    'data-type-pattern' => "email"
                ],
            ])
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
            ->add('send', SubmitType::class, [
                'label' => 'Envoyer',
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
