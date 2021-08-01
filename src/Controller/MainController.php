<?php

namespace App\Controller;

use App\Entity\Jeziora;
use App\Form\KontaktType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $jezioraOstatnie = $entityManager->getRepository(Jeziora::class)->findByOstatnieLosowe();
        #$jezioraOstatnie = $entityManager->getRepository(Jeziora::class)->findOstatnie();

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'jeziora'   => $jezioraOstatnie,
        ]);
    }

    /**
     * @Route("/cennik", name="cennik")
     */
    public function cennik()
    {
        


        return $this->render('main/cennik.html.twig', [
            'controller_name' => 'CennikController',
            'current_menu'  => 'cennik',
        ]);
    }

    /**
     * @Route("/info", name="info")
     */
    public function info()
    {
        return $this->render('main/info.html.twig', [

            'current_menu'  => 'info',
        ]);
    }

    /**
     * @Route("/kontakt", name="kontakt")
     */
    public function kontakt(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(KontaktType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $kontakt = $form->getData();
            
            // Create a message
            $wiadomosc = (new \Swift_Message('www.e-jeziora24.pl - nowa wiadomosc'))
                #kto wysyła
                ->setFrom($kontakt['email'])

                #do kogo
                ->setTo('biuro@e-jeziora24.pl')

                #wiadomosc
                ->setBody(
                    $this->renderView(
                        'email/kontakt.html.twig', compact('kontakt')
                    ),
                    'text/html'
                )
            ;

            // Send the message
            $mailer->send($wiadomosc);

            $this->addFlash('success', " Wiadomość wysłana pomyślnie.");

            return $this->redirectToRoute('jeziora_index');

        }

        return $this->render('main/kontakt.html.twig', [
            'current_menu'  => 'kontakt',
            'kontaktForm'   => $form->createView(),
        ]);
    }
}
