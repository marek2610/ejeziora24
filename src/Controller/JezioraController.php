<?php

namespace App\Controller;

use App\Entity\Jeziora;
use App\Entity\JezioraSearch;
use App\Entity\JezioraWiadomosc;
use App\Entity\Oplaty;
use App\Entity\Users;
use App\Form\JezioraSearchType;
use App\Form\JezioraType;
use App\Form\JezioraWiadomoscType;
use App\Repository\JezioraRepository;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/jeziora")
 */
class JezioraController extends AbstractController
{

    /**
     * @Route("/", name="jeziora_index", methods={"GET"})
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {

        #throw $this->createNotFoundException("Strona nie istnieje");

        $search = new JezioraSearch();

        $form = $this->createForm(JezioraSearchType::class, $search);
        $form->handleRequest($request);

        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Jeziora::class)->findWszystkie($search);

        //dump($query);

        $jezioraWszystkie = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );

        return $this->render('jeziora/index.html.twig', [
            'current_menu'  => 'index',
            'jeziora'   => $jezioraWszystkie,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="jeziora_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        // $user = $this->getUser();

        // if (!$user->getImie()) {

        //     $this->addFlash('error', " Dodanie nowego jeziora zakończone niepowodzeniem");

        // } else {

        $jeziora = new Jeziora();
        $form = $this->createForm(JezioraType::class, $jeziora);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {

            if ($form->isSubmitted() && $form->isValid()) {

                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('brochure')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $brochureFile->move(
                            $this->getParameter('brochures_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $jeziora->setBrochureFilename($newFilename);
                } else {
                    #przykłądowe zdjęcie z serwera
                    $jeziora->setBrochureFilename('klaffer-2834677_960_720.jpg');
                }

                $jeziora->setUsers($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($jeziora);
                $entityManager->flush();

                $this->addFlash('success', " Nowe jezioro zostało dodane.");

                return $this->redirectToRoute('jeziora_index');
            }

            $this->addFlash('error', " Dodanie nowego jeziora zakończone niepowodzeniem");
        }


        return $this->render('jeziora/new.html.twig', [
            'jeziora' => $jeziora,
            'form' => $form->createView(),
            'current_menu'  => 'dodaj',
        ]);
    }

    /**
     * @Route("/{slug}", name="jeziora_show", methods={"GET", "POST"})
     */
    public function show(Jeziora $jeziora, Request $request, \Swift_Mailer $mailer): Response
    {
        #throw $this->createNotFoundException("Błąd");

        #trzeba dopracować błąd wpisania gdy nie takiego sluga
        #należy odświerzyyć po wprowadzeniu zmian do .env
        if (!$jeziora) {
            throw $this->createNotFoundException("Strona nie istnieje");
        }

 
        #dump($jeziora);
       
        ####################################################################
        #pobieranie danych dla usera w sprawie opłat i kart wędkarskich
        $user = $jeziora->getUsers();
        #dump($user);

        $entityManager = $this->getDoctrine()->getManager();
        $oplatyUser = $entityManager->getRepository(Oplaty::class)->findBy([
            'user' => $user,
        ]);
        ####################################################################

        ####################################################################
        #pobieranie danych dla jeziora w sprawie opłat i kart wędkarskich
        $jezioroOplaty = $jeziora->getId();
        #dump($jezioroOplaty);
        $entityManager = $this->getDoctrine()->getManager();
        $oplatyJezioroID = $entityManager->getRepository(Oplaty::class)->findBy([
            'jezioro' => $jezioroOplaty,
        ]);
        ####################################################################
            
        #dump($user);

        #tworzenie encji wiadomości do Usera
        $wiadomosc = new JezioraWiadomosc();

        #pobieranie danych z formularza
        $form = $this->createForm(JezioraWiadomoscType::class, $wiadomosc);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {

            if ($form->isSubmitted() && $form->isValid()) {

                #pobieranie danych do emaila
                $email = $form->getData();

                #tworzenie wiadomosci email
                $wiadomoscEmail = (new \Swift_Message())
                    ->setFrom('biuro@e-jeziora24.pl')
                    ->setTo($jeziora->getUsers()->getEmail())
                    ->setCC($email->getEmail())
                    ->setSubject('www.e-jeziora24.pl - ' . $jeziora->getNazwa())
                    ->setBody(
                        $this->renderView(
                            'email/wiadomosc.html.twig', compact('email')
                        ),
                        'text/html'
                    )
                ;

                // Send the message
                $mailer->send($wiadomoscEmail);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($wiadomosc);
                $entityManager->flush();

                $this->addFlash('success', 'Email wysłany pomyślnie.');

                return $this->redirectToRoute('jeziora_show', [
                    'slug'  => $jeziora->getSlug(),
                ]);

            }
        }

        return $this->render('jeziora/show.html.twig', [
            'jeziora' => $jeziora,
            'form'  => $form->createView(),
            'oplaty' => $oplatyUser,
            'user'  => $user,
            'oplatyJezioroID' => $oplatyJezioroID,
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="jeziora_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Jeziora $jeziora, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        # sprawdzenie czy użytkownik jest właścicielem
        # ogłoszenia które zostało dodane jeżeli nie, 
        # dostęp zostaje zablokowany
        if ($this->getUser() !== $jeziora->getUsers()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(JezioraType::class, $jeziora);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('brochure')->getData();

            if ($brochureFile) {

                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' .

                $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $jeziora->setBrochureFilename($newFilename);

            }
                
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', " Jezioro zostało pomyślnie zmodyfikowane.");

            return $this->redirectToRoute('jeziora_index');
        }

        return $this->render('jeziora/edit.html.twig', [
            'jeziora' => $jeziora,
            'form' => $form->createView(),
            'current_menu'  => 'profil'
        ]);
    }

    /**
     * @Route("/{id}", name="jeziora_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Jeziora $jeziora): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        # sprawdzenie czy użytkownik jest właścicielem
        # ogłoszenia które zostało dodane jeżeli nie, 
        # dostęp zostaje zablokowany
        if ($this->getUser() !== $jeziora->getUsers()) {
            throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$jeziora->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jeziora);
            $entityManager->flush();
        }

        return $this->redirectToRoute('jeziora_index');
    }

    /**
     * @Route("/jeziora/user", name="jeziora_user")
     */
    public function user(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $query = $entityManager->getRepository(Jeziora::class)->findBy([
            'users'  => $user,
        ]);

        $jezioraUser = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );

        #dump($jezioraUser);

        return $this->render('jeziora_user/index.html.twig', [
            'current_menu'  => 'moje',
            'jezioraUser'   => $jezioraUser,
        ]);
    }

    /**
     * @Route("uzytkownicy", name="jeziora_uzytkownicy")
     */
    public function uzytkownicy()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $licznikJezioraUzytkownicy = $entityManager->getRepository(Jeziora::class)->findLicznikJezior();

        #dump($licznikJezioraUzytkownicy);
        
        return $this->render('jeziora/users.html.twig', [
            'current_menu'  => 'lista',
            'licznikJezioraUzytkownicy'   => $licznikJezioraUzytkownicy,
        ]);
    }

    /**
     * @Route("uzytkownicy/{id}", name="jeziora_uzytkownicy_oferta")
     */
    public function uzytkownicyJeziora(Users $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $licznikJezioraUzytkownicy = $entityManager->getRepository(Jeziora::class)->findWszystkieJezioraUsera($user);

        #dump($licznikJezioraUzytkownicy);
        
        return $this->render('jeziora/index_user.html.twig', [
            'current_menu'  => 'lista',
            'licznikJezioraUzytkownicy'   => $licznikJezioraUzytkownicy,
            'user' => $user,
        ]);
    }

    /**
     * @Route("uzytkownicy/karty/{id}", name="jeziora_uzytkownicy_karty")
     */
    public function uzytkownicyKarty()
    {
        
    }
}
