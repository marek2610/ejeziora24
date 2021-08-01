<?php

namespace App\Form;

use App\Entity\Jeziora;
use App\Entity\Region;
use Doctrine\DBAL\Types\DecimalType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class JezioraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nazwa', TextType::class)
            ->add('miejscowosc', TextType::class)
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'placeholder' => 'Wybierz',
                'required' => false,
            ])
            ->add('powierzchnia')
            ->add('opis', TextareaType::class, [
                'required'   => false,
                'empty_data' => '-'
            ])
            ->add('pomosty', ChoiceType::class, [
                // 'placeholder' => 'Wybierz',
                'required' => false,
                'empty_data' => false,
                'mapped' => false,
                'expanded'  => true,
                'multiple'  => true,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('lodz', ChoiceType::class, [
                // 'placeholder' => 'Wybierz',
                'required' => false,
                'empty_data' => false,
                'mapped' => false,
                'expanded'  => true,
                'multiple'  => true,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('kusza', ChoiceType::class, [
                // 'placeholder' => 'Wybierz',
                'required' => false,
                'empty_data' => false,
                'mapped' => false,
                'expanded'  => true,
                'multiple'  => true,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('fish', TextType::class, [
                'required'   => false,
                'empty_data' => ' ',
            ])
            ->add('brochure', FileType::class, [
                'label' => 'Zdjęcie',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Proszę załączyć plik ze zdjęciem',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Jeziora::class,
        ]);
    }
}
