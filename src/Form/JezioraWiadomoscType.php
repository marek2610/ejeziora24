<?php

namespace App\Form;

use App\Entity\JezioraWiadomosc;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JezioraWiadomoscType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imie', TextType::class, [
                'label' => false,
                'required'   => true,
                'attr' => [
                    'placeholder' => 'np. Jan'
                ]
            ])
            ->add('nazwisko', TextType::class, [
                'label' => false,
                'required'   => true,
                'attr' => [
                    'placeholder' => 'np. Nowak'
                ]
            ])
            ->add('telefon', TextType::class, [
                'label' => false,
                'required'   => true,
                'attr' => [
                    'placeholder' => 'np. 123 456 789'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'required'   => true,
                'attr' => [
                    'placeholder' => 'przykadowy@adres.email.pl'
                ]
            ])
            ->add('wiadomosc', TextareaType::class, [
                'label' => false,
                'required'   => true,
                'attr' => [
                    'placeholder' => 'Przykładowe zapytanie na temat jeziora. "Dzień dobry. Proszę o informację czy....."'
                ]
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => false,
            ])
            #->add('wyslij', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JezioraWiadomosc::class,
        ]);
    }
}
