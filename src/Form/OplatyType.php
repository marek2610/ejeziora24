<?php

namespace App\Form;

use App\Entity\Jeziora;
use App\Entity\Oplaty;
use App\Entity\Users;
use App\Repository\JezioraRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class OplatyType extends AbstractType
{
    private $security;

    public function __construct(CoreSecurity $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        #pobieranie użytkownika aktualnie zalogowanego
        #rozwiazanie https://symfony.com/doc/current/form/dynamic_form_modification.html
        $user = $this->security->getUser();
        if (!$user) {
            throw new \LogicException(
                'The FriendMessageFormType cannot be used without an authenticated user!'
            );
        }

        $builder
            ->add('rodzaj')
            ->add('cena')
            ->add('jezioro', EntityType::class, [
                'class' => Jeziora::class,
                'empty_data' => '0',
                'placeholder'   => 'Wybierz',
                'query_builder' => function (JezioraRepository $repo) use ($user){
                    return $repo->createQueryBuilder('j')
                        ->select('j')
                        ->join(Users::class, 'u', 'WITH', 'j.users = u.id')
                        ->andWhere('j.aktywny = true')
                        ->andWhere('j.users = :user')
                        ->setParameter('user', $user)
                        ->orderBy('j.utworzono', 'ASC');
                }
            ])             
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Oplaty::class,
        ]);
    }
}
