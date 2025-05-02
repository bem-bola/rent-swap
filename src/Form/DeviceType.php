<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\TypeDevice;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('body', TextareaType::class, [
                'label' => 'Description',
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'La description est obligatoire',
//                    ]),
//                    new Regex([
//                        'pattern' => '/^[A-Za-zÀ-ÖØ-öø-ÿ-0-9]$/',
//                        'message' => 'La description est obligatoire',
//                    ]),
//                ],

            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'required' => false,
            ])
            ->add('showPhone', CheckboxType::class, [
                'label' => 'Afficher son numéro de télephone ?',
                'required' => false
            ])

            ->add('title', TextType::class, [
                'label' => 'Titre',
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Le titre est obligatoire',
//                    ]),
//                    new Regex([
//                        'pattern' => '/^[A-Za-zÀ-ÖØ-öø-ÿ-0-9]$/',
//                        'message' => 'Le titre ne contient de caractères non autorisés',
//                    ]),
//                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'N° téléphone',
//                'constraints' => [
//                    new Regex([
//                        'pattern' => '/^[0-9 ]$/',
//                        'message' => 'Le télephone contient des caractères non autorisés',
//                    ]),
//                ],
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
            ])

            ->add('type', EntityType::class, [
                'class' => TypeDevice::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ])

            ->add('draft', SubmitType::class, [
                'label' => 'Sauvegarder',
            ])
            ->add('next', SubmitType::class, [
                'label' => 'Suivant',
            ])
            ->add('publish', SubmitType::class, [
                'label' => 'Publier',
            ]);

        if($options['create'] === false)
            $builder->add('delete', SubmitType::class, [
                'label' => 'Supprimer',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Device::class,
            'create'     => false
        ]);
    }
}
