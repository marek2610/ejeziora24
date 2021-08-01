<?php

namespace App\Form;

use App\Entity\Jeziora;
use App\Entity\Oplaty;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminOplatyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => Users::class,
                'placeholder' => 'Użytkownik',
                'choice_label' => 
                function(Users $users) {
                    return sprintf('%s %s %s - %s', $users->getImie(), $users->getNazwisko(), $users->getNazwa(), $users->getEmail());
                },
            ])
            ->add('jezioro', EntityType::class, [
                'class' => Jeziora::class, 
                'placeholder' => 'Wybierz jezioro',
                'choice_label' => 
                function(Jeziora $jeziora) {
                    return sprintf('%s - %sha, msc.: %s', $jeziora->getNazwa(), $jeziora->getPowierzchnia(), $jeziora->getMiejscowosc());
                },
            ])
            ->add('rodzaj')
            ->add('cena')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Oplaty::class,
        ]);
    }
}
