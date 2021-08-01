<?php

namespace App\Form;

use App\Entity\JezioraSearch;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JezioraSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nazwaSearch', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => [
                    'placeholder'   => 'Nazwa jeziora'
                ]
            ])
            ->add('miejscowoscSearch', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => [
                    'placeholder'   => 'Nazwa miejscowosci'
                ]
            ])
            ->add('powierzchniaSearch', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => [
                    'placeholder'   => 'Min. powierzchnia jeziora np. 6ha'
                ]
            ])
            ->add('pomostySearch', ChoiceType::class, [
                'placeholder' => 'Wybierz',
                'label' => 'Pomosty na jeziorze',
                'required' => false,
                'expanded'  => false,
                'multiple'  => false,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('lodzSearch', ChoiceType::class, [
                'placeholder' => 'Wybierz',
                'label' => 'Połowy łodzią',
                'required' => false,
                'expanded'  => false,
                'multiple'  => false,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('kuszaSearch', ChoiceType::class, [
                'placeholder' => 'Wybierz',
                'label' => 'Połowy kuszą',
                'required' => false,
                'expanded'  => false,
                'multiple'  => false,
                'choices'   => [
                    'Tak'   => true,
                ]
            ])
            ->add('regionSearch', EntityType::class, [
                'class' => Region::class, 
                'placeholder'   => 'Wybierz',
                'required'  => false,
                'label' => 'Województwo',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => JezioraSearch::class,
            'method'    => 'get',
            'csrf_protection'   => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
