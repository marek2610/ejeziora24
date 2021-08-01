<?php

namespace App\Form;

use App\Entity\Jeziora;
use App\Entity\Region;
use App\Entity\Users;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminJezioraEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('aktywny')
            ->add('users', EntityType::class, [
                'class' => Users::class,
                'placeholder' => 'Wybież użytkownika',
                'choice_label' => 
                function(Users $users) {
                    return sprintf('%s %s %s - %s', $users->getImie(), $users->getNazwisko(), $users->getNazwa(), $users->getEmail());
                },
            ])
            ->add('nazwa', TextType::class)
            ->add('slug')
            ->add('powierzchnia')
            ->add('miejscowosc')
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'placeholder' => 'Wybierz',
                'required' => false,
            ])
            ->add('pomosty', ChoiceType::class, [
                // 'placeholder' => 'Wybierz',
                'required' => false,
                'choices'   => [
                    'Tak'   => true,
                    'Nie'   => false,
                    ]
            ])
            ->add('lodz', ChoiceType::class, [
                // 'placeholder' => 'Wybierz',
                'required' => false,
                'choices'   => [
                    'Tak'   => true,
                    'Nie'   => false,
                ]
            ])
            ->add('kusza', ChoiceType::class, [
                    // 'placeholder' => 'Wybierz',
                'required' => false,
                'choices'   => [
                    'Tak'   => true,
                    'Nie'   => false,
                    ]
            ])
            ->add('fish')
            ->add('opis', CKEditorType::class)
            // ->add('utworzono', DateType::class, [
            //     'widget' => 'single_text',
            // ])
            // ->add('modyfikacja', DateType::class, [
            //     'widget' => 'single_text',
            // ])
            // ->add('brochure', FileType::class, [
            //     'label' => 'Zdjęcie',

            //     // unmapped means that this field is not associated to any entity property
            //     'mapped' => false,

            //     // make it optional so you don't have to re-upload the PDF file
            //     // every time you edit the Product details
            //     'required' => false,

            //     // unmapped fields can't define their validation using annotations
            //     // in the associated entity, so you can use the PHP constraint classes
            //     'constraints' => [
            //         new File([
            //             'maxSize' => '2M',
            //             'mimeTypes' => [
            //                 'image/*',
            //             ],
            //             'mimeTypesMessage' => 'Proszę załączyć plik ze zdjęciem',
            //         ])
            //     ],
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Jeziora::class,
        ]);
    }
}
