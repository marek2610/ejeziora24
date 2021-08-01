<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KontaktType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imie', TextType::class)
            ->add('nazwisko', TextType::class)
            ->add('telefon', TextType::class)
            ->add('email', EmailType::class)
            ->add('wiadomosc', CKEditorType::class)
            ->add('captcha', CaptchaType::class, [
                'label' =>false,
            ])
            #->add('wyslij', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
