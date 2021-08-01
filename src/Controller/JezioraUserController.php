<?php

namespace App\Controller;

use App\Form\UsersProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users")
 */
class JezioraUserController extends AbstractController
{
    /**
     * @Route("/", name="user.index", methods={"GET"})
     */
    public function index()
    {
        return $this->render('users/index.html.twig', [
          'current_menu'  => 'profil',
        ]);
    }

    /**
     * @Route("/edit", name="users.edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UsersProfileType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {

            
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', "Edycja profilu zakończona sukcesem");
                
                return $this->redirectToRoute('users_index');
            }
            
            $this->addFlash('error', "Błąd podczas zapisywania");

        }

        return $this->render('users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/password", name="users.password")
     */
    public function passwordEdit(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if  ($request->isMethod('post')) {

            $entityManager = $this->getDoctrine()->getManager();

            $user = $this->getUser();

            #weryfikacja dwóch haseł
            if ($request->request->get('password1') == $request->request->get('password2')) {
                
                $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password1')));
                $entityManager->flush();
                $this->addFlash('success', "Hasło zostało zmienione.");

                return $this->redirectToRoute('users_index');

            } else {

                $this->addFlash('error', "Hasła nie są identyczne.");

            }


        }

        return $this->render('users/edit_pass.html.twig');
    }
}