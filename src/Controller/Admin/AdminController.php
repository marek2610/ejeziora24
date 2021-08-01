<?php

namespace App\Controller\Admin;

use App\Entity\Jeziora;
use App\Entity\JezioraKategoria;
use App\Entity\JezioraWiadomosc;
use App\Entity\Oplaty;
use App\Entity\Region;
use App\Entity\Users;
use App\Form\AdminFotoType;
use App\Form\AdminJezioraEditType;
use App\Form\AdminJezioraType;
use App\Form\AdminOplatyType;
use App\Form\AdminUsersType;
use App\Form\JezioraType;
use App\Form\OplatyType;
use App\Form\UsersType;
use App\Repository\OplatyRepository;
use App\Security\UsersAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/admin", name="admin_admin")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(Users::class)->findAll();
        $usersActive = $entityManager->getRepository(Users::class)->findBy(['isVerified' => true]);
        $usersNotActive = $entityManager->getRepository(Users::class)->findBy(['isVerified' => false]);

        $jeziora = $entityManager->getRepository(Jeziora::class)->findAll();
        $jezioraActive = $entityManager->getRepository(Jeziora::class)->findBy(['aktywny' => true]);
        $jezioraNotActive = $entityManager->getRepository(Jeziora::class)->findBy(['aktywny' => false]);
        $jezioraWoj = $entityManager->getRepository(Jeziora::class)->findByRegion();

        //dump($jezioraWoj);

        $region = $entityManager->getRepository(Region::class)->findAll();
        
        $jezioraWiadomosci = $entityManager->getRepository(JezioraWiadomosc::class)->findAll();

        $oplaty = $entityManager->getRepository(Oplaty::class)->findAll();

        $foto = $entityManager->getRepository(Jeziora::class)->findBySystemFoto();

        $kategoria = $entityManager->getRepository(JezioraKategoria::class)->findAll();

        // $session = $this->get('session');
        // $session->set('users', $users);
        // $session->set('jeziora', $jeziora);
        // $session->set('region', $region);
        // $session->set('jezioraWiadomosci', $jezioraWiadomosci);
        // $session->set('oplaty', $oplaty);

        return $this->render('admin/admin/index.html.twig', [
            'users'     => $users,
            'usersActive'  => $usersActive,
            'usersNotActive'  => $usersNotActive,
            'jeziora'   => $jeziora,
            'jezioraActive'   => $jezioraActive,
            'jezioraNotActive'   => $jezioraNotActive,
            'jezioroWoj'  => $jezioraWoj,
            'foto'  => $foto,
            'region'    => $region,
            'jezioraWiadomosci' => $jezioraWiadomosci,
            'oplaty'    => $oplaty,
            'kategoria' => $kategoria,
            'current_menu'  => 'admin',
        ]);
    }

    /**
     * @Route("/admin/users", name="admin.users.index")
     */
    public function indexAdminUsers()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $users = $entityManager->getRepository(Users::class)->findAll();

        return $this->render('admin/users/index.html.twig', [
            'users'     => $users,
            'current_menu'  => 'admin',
        ]);
    }

    /**
     * @Route("/admin/user/new", name="admin.user.new")
     */
    public function newAdminUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UsersAuthenticator $authenticator): Response
    {
        $user = new Users();
        $form = $this->createForm(AdminUsersType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', "Dodawanie nowego urzytkownika {$user->getEmail()} - {$user->getImie()} {$user->getNazwisko()} {$user->getNazwa()} zakończone pomyślnie.");

            // // generate a signed url and email it to the user
            // $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            //     (new TemplatedEmail())
            //         #->from(new Address('biuro@e-jeziora24.pl', 'Rejestracja - www.e-jeziora24.pl'))
            //         ->from(new Address('biuro@e-jeziora24.pl', 'Rejestracja - www.e-jeziora24.pl'))
            //         ->to($user->getEmail())
            //         ->subject('Rejestracja w serwisie www.e-jeziora24.pl')
            //         ->htmlTemplate('registration/confirmation_email.html.twig')
            // );
            // // do anything else you need here, like send an email

            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );

            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
            'current_menu'  => 'register',
        ]);
    }


    /**
     * @Route("/admin/user/info/{id}", name="admin.user.info")
     */
    public function infoAdminUser(Request $request, Users $user)
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("admin/user/{id}/edit", name="admin.user.edit", methods={"GET","POST"})
     */
    public function editAdminUser(Request $request, Users $user): Response
    {
        $form = $this->createForm(UsersType::class, $user);
        //$form = $this->createForm(AdminUsersEditType::class, $user);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                
                $this->addFlash('success', " Modyfikacja użytkownika {$user->getEmail()} zakończona sukcesem.");
                
                return $this->redirectToRoute('admin.users.index');
            }

            $this->addFlash('error', " Modyfikacja użytkownika {$user->getEmail()} zakończona niepowoodzeniem.");
            
        }
        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin.user.delete")
     */
    public function deleteAdminUser(Request $request, Users $user)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', " Pomyślnie usunięto użytkownika {$user->getEmail()} - {$user->getImie()} {$user->getNazwisko()} {$user->getNazwa()} zakończona sukcesem.");
        
        return $this->redirectToRoute('admin.users.index');

    }

    #########################################################################

    ### Admin Jeziora
    ### Zestawienie jezior
    /**
     * @Route("/admin/jeziora", name="admin.jeziora.index")
     */
    public function indexAdminJeziora()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $jeziora = $entityManager->getRepository(Jeziora::class)->findBy([],[
            'aktywny'   => 'DESC'
        ]);

        return $this->render('admin/jeziora/index.html.twig', [
            'jeziora'   => $jeziora,
            'current_menu'  => 'admin',
        ]);
    }

    ### Info konkretnego jeziora
    /**
     * @Route("/admin/jeziora/info/{slug}", name="admin.jeziora.show", methods={"GET"})
     */
    public function show(Jeziora $jezioro): Response
    {
        return $this->render('admin/jeziora/show.html.twig', [
            'jezioro' => $jezioro,
        ]);
    }

    ### Edycja konkretnego jeziora
    /**
     * @Route("/admin/jeziora/{slug}/edit", name="admin.jeziora.edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Jeziora $jezioro): Response
    {
        $form = $this->createForm(AdminJezioraEditType::class, $jezioro);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {
            
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', " Modyfikacja jeziora {$jezioro->getNazwa()} zakończona sukcesem.");
                
                return $this->redirectToRoute('admin.jeziora.index');
            }

            $this->addFlash('error', " Modyfikacja jeziora {$jezioro->getNazwa()} zakończona niepowodzeniem");
            
        }
        return $this->render('admin/jeziora/edit.html.twig', [
            'jezioro' => $jezioro,
            'form' => $form->createView(),
        ]);
    }

    ### delete jeziora
    /**
     * @Route("/admin/jeziora/{slug}", name="admin.jeziora.delete")
     */
    public function deleteAdminJezioro(Request $request, Jeziora $jeziora)
    {

        $entityManager = $this->getDoctrine()->getManager();
        
        if ($entityManager->remove($jeziora) == true) {
            
            $entityManager->remove($jeziora);
            $entityManager->flush();
    
            $this->addFlash('success', " Usunięcie jeziora {$jeziora->getNazwa()} zakończona sukcesem.");

        } else {

            $this->addFlash('error', " Nie można usunąć jeziora {$jeziora->getNazwa()} ze względu na istniejące powiązania np. dostępne kart. Najpierw proszę usunąć opłaty za wędkowanie dla jeziora {$jeziora->getNazwa()}.");

        }
        return $this->redirectToRoute('admin.jeziora.index');

    }

    ### new jeziora - trzeba zmienić adnotacje na JEZIORA a 
    /**
     * @Route("/admin/jezioro/new", name="admin.jeziora.new", methods={"GET","POST"})
     */
    public function newAdminJezioro(Request $request, SluggerInterface $slugger): Response
    {
        $jeziora = new Jeziora();
        $form = $this->createForm(AdminJezioraType::class, $jeziora);
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

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($jeziora);
                $entityManager->flush();

                $this->addFlash('success', " Nowe jezioro {$jeziora->getNazwa()} zostało dodane.");

                return $this->redirectToRoute('admin.jeziora.index');
            }

            $this->addFlash('error', " Dodanie nowego jeziora zakończone niepowodzeniem");
        }


        return $this->render('admin/jeziora/new.html.twig', [
            'jeziora' => $jeziora,
            'form' => $form->createView(),
        ]);
    }



    
    #########################################################################


    ### WOJEWÓDZTWA
    ### Index województw
    /**
     * @Route("/admin/region", name="admin.region.index")
     */
    public function indexAdminRegion()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $region = $entityManager->getRepository(Region::class)->findAll();

        return $this->render('admin/region/index.html.twig', [
            'region' => $region,
        ]);
    }

    ### Info konkretnego jeziora
    /**
     * @Route("/admin/region/info/{id}", name="admin.region.show", methods={"GET"})
     */
    public function showAdminRegion(Region $region): Response
    {
        return $this->render('admin/region/show.html.twig', [
            'region' => $region,
        ]);
    }

    #########################################################################

    ### WIADOMOŚCI
    ### Index wiadomości
    /**
     * @Route("/admin/wiadomosci", name="admin.wiadomosci.index")
     */
    public function indexAdminWiadomosci()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $jezioraWiadomosci = $entityManager->getRepository(JezioraWiadomosc::class)->findAll();

        return $this->render('admin/wiadomosci/index.html.twig', [
            'wiadomosci' => $jezioraWiadomosci,
        ]);
    }

    ### Info wiadomości
    /**
     * @Route("/admin/wiadomosci/info/{id}", name="admin.wiadomosci.show", methods={"GET"})
     */
    public function showAdminWiadomosci(JezioraWiadomosc $jezioraWiadomosc): Response
    {
        return $this->render('admin/wiadomosci/show.html.twig', [
            'jezioraWiadomosc' => $jezioraWiadomosc,
        ]);
    }

    ### Kasowanie wiadomości do użytkownika
    /**
     * @Route("/admin/wiadomosci/{id}", name="admin.wiadomosci.delete")
     */
    public function deleteAdminWiadomosci(Request $request, JezioraWiadomosc $jezioraWiadomosc)
    {
        dump($jezioraWiadomosc);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($jezioraWiadomosc);
        $entityManager->flush();

        $this->addFlash('success', " Usunięcie wiadomosci od {$jezioraWiadomosc->getImie()} {$jezioraWiadomosc->getNazwisko()} zakończona sukcesem.");

        return $this->redirectToRoute('admin.wiadomosci.index');
    }

    #########################################################################


    ### karty wędkarskie
    /**
     * @Route("/admin/karty/info", name="admin.oplaty.index")
     */
    public function indexAdminKarty()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $oplaty = $entityManager->getRepository(Oplaty::class)->findByAdminOplaty();
        $jezioraBezOplaty = $entityManager->getRepository(Jeziora::class)->findByAdminWszystkieJezioraUseraBezOplat();

        return $this->render('admin/oplaty/index.html.twig', [
            'oplaty' => $oplaty,
            'jeziora_bez_oplat'   => $jezioraBezOplaty,
        ]);
    }

    /**
     * @Route("/admin/karty/info/{id}", name="admin.karty.show", methods={"GET"})
     */
    public function showAdminKarty(Oplaty $oplaty): Response
    {
        return $this->render('admin/oplaty/show.html.twig', [
            'oplaty' => $oplaty,
        ]);
    }

    /**
     * @Route("/admin/karty/new", name="admin.karty.new", methods={"GET","POST"})
     */
    public function newAdminKarty(Request $request): Response
    {
        $oplaty = new Oplaty();
        $form = $this->createForm(AdminOplatyType::class, $oplaty, );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
  
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oplaty);
            $entityManager->flush();

            return $this->redirectToRoute('admin.oplaty.index');
        }

        return $this->render('admin/oplaty/new.html.twig', [
            'oplaty'    => $oplaty,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/karty/{id}", name="admin.karty.delete")
     */
    public function deleteAdminKarty(Request $request, Oplaty $oplaty)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($oplaty);
        $entityManager->flush();

        $this->addFlash('success', " Usunięcie opłaty{$oplaty->getRodzaj()} dla {$oplaty->getJezioro()} zakończona sukcesem.");

        return $this->redirectToRoute('admin.oplaty.index');
    }

    ### Edycja konkretnej karty
    /**
     * @Route("/admin/karty/{id}/edit", name="admin.karty.edit", methods={"GET","POST"})
     */
    public function editAdminKarty(Request $request, Oplaty $oplata): Response
    {
        $form = $this->createForm(AdminOplatyType::class, $oplata);
        $form->handleRequest($request);

        if ($request->isMethod('post')) {
            
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', " Modyfikacja jeziora {$oplata->getRodzaj()} zakończona sukcesem.");
                
                return $this->redirectToRoute('admin.oplaty.index');
            }

            $this->addFlash('error', " Modyfikacja jeziora {$oplata->getRodzaj()} zakończona niepowodzeniem");
            
        }
        return $this->render('admin/oplaty/edit.html.twig', [
            'oplata' => $oplata,
            'edit'  => 'edit',
            'form' => $form->createView(),
        ]);
    }


    #########################################################################


    ### Zdjęcia wędkarskie
    ### zestawieei zdjęć
    /**
     * @Route("/admin/foto", name="admin.foto.index")
     */ 
    public function indexAdminFoto()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $foto = $entityManager->getRepository(Jeziora::class)->findByFotoAdmin();

        return $this->render('admin/foto/index.html.twig', [
            'foto'  => $foto,
        ]);

    }

    ### Info konkretnego zdjęcia
    /**
     * @Route("/admin/foto/info/{slug}", name="admin.foto.show", methods={"GET"})
     */
    public function showAdminFoto(Jeziora $jezioro): Response
    {
        return $this->render('admin/foto/show.html.twig', [
            'jezioro' => $jezioro,
        ]);
    }

    ### Edycja konkretnego zdjęcia
    /**
     * @Route("/admin/foto/{slug}/edit", name="admin.foto.edit", methods={"GET","POST"})
     */
    public function editAdminFoto(Request $request, Jeziora $jezioro, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(AdminFotoType::class, $jezioro);
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
                    $jezioro->setBrochureFilename($newFilename);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($jezioro);
                    $entityManager->flush();
    
                    $this->addFlash('success', " Modyfikacja zdjęcia dla {$jezioro->getNazwa()} zakończona sukcesem.");

                } else {
                    #przykłądowe zdjęcie z serwera
                    #$jezioro->setBrochureFilename('klaffer-2834677_960_720.jpg');

                    #zdjęcie nie zostało wybrane przez USera a wciśnięt został button "modyfikuj"
                    $this->addFlash('error', " Modyfikacja zdjęcia dla jeziora {$jezioro->getNazwa()} zakończona niepowodzeniem");
                }
                
                
                return $this->redirectToRoute('admin.foto.index');
            }

            $this->addFlash('error', " Modyfikacja zdjęcia dla jeziora {$jezioro->getNazwa()} zakończona niepowodzeniem");
            
        }
        return $this->render('admin/foto/edit.html.twig', [
            'foto' => $jezioro,
            'jezioro' => $jezioro,
            'form' => $form->createView(),
        ]);
    }
    
}