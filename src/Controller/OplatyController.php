<?php

namespace App\Controller;

use App\Entity\Oplaty;
use App\Entity\Jeziora;
use App\Entity\Users;
use App\Form\OplatyType;
use App\Repository\JezioraRepository;
use App\Repository\OplatyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/oplaty")
 */
class OplatyController extends AbstractController
{
    /**
     * @Route("/", name="oplaty_index", methods={"GET"})
     */
    public function index(OplatyRepository $oplatyRepository, JezioraRepository $jezioraRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $oplaty = $entityManager->getRepository(Oplaty::class)->findBy([
            'user'  => $user,
        ]);

        $jezioraBezOplat = $entityManager->getRepository(Jeziora::class)->findByWszystkieJezioraUseraBezOplat($user);

        return $this->render('oplaty/index.html.twig', [
            'current_menu'  => 'oplaty',
            #'oplaty' => $oplatyRepository->findAll(),
            'oplaty' => $oplaty,
            'jeziora_bez_oplat' => $jezioraBezOplat,
        ]);
    }

    /**
     * @Route("/new", name="oplaty_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();

        $oplaty = new Oplaty();
        $form = $this->createForm(OplatyType::class, $oplaty, );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $oplaty->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oplaty);
            $entityManager->flush();

            return $this->redirectToRoute('oplaty_index');
        }

        return $this->render('oplaty/new.html.twig', [
            'oplaty' => $oplaty,
            'form' => $form->createView(),
            'user'  => $user,
        ]);
    }

    /**
     * @Route("/{id}", name="oplaty_show", methods={"GET"})
     */
    public function show(Oplaty $oplaty): Response
    {
        return $this->render('oplaty/show.html.twig', [
            'oplaty' => $oplaty,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="oplaty_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Oplaty $oplaty): Response
    {
        $form = $this->createForm(OplatyType::class, $oplaty);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('oplaty_index');
        }

        return $this->render('oplaty/edit.html.twig', [
            'oplaty' => $oplaty,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="oplaty_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Oplaty $oplaty): Response
    {
        if ($this->isCsrfTokenValid('delete'.$oplaty->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($oplaty);
            $entityManager->flush();
        }

        return $this->redirectToRoute('oplaty_index');
    }
}
