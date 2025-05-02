<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DropzoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('pictures', DropzoneType::class, [
                'label' => 'Ajouter des images',
                'mapped' => false,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple',
                    'placeholder' => 'Cliquez-déposez des images ou cliquez pour pour parcourir',
                    'data-dropzone-action' => 'uploadFiles',
                    'data-dropzone-auto-upload' => 'true',

                ],
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'maxSize' => '2M',
                        'mimeTypesMessage' => 'Veuillez charger des images au format JPEG ou PNG',
                    ])
                ]
            ])

            ->add('draft', SubmitType::class, [
                'label' => 'Quitter et enregistrer dans le brouillon'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
