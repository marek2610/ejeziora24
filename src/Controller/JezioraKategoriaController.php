<?php

namespace App\Controller;

use App\Entity\JezioraKategoria;
use App\Form\JezioraKategoriaType;
use App\Repository\JezioraKategoriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/kategoria")
 */
class JezioraKategoriaController extends AbstractController
{
    /**
     * @Route("/", name="jeziora_kategoria_index", methods={"GET"})
     */
    public function index(JezioraKategoriaRepository $jezioraKategoriaRepository): Response
    {
        return $this->render('admin/jeziora_kategoria/index.html.twig', [
            'jeziora_kategorias' => $jezioraKategoriaRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="jeziora_kategoria_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $jezioraKategorium = new JezioraKategoria();
        $form = $this->createForm(JezioraKategoriaType::class, $jezioraKategorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($jezioraKategorium);
            $entityManager->flush();

            $this->addFlash('success', "Pomyślnie dodano nowa kategorię obiektu");

            return $this->redirectToRoute('jeziora_kategoria_index');
        }

        return $this->render('admin/jeziora_kategoria/new.html.twig', [
            'jeziora_kategorium' => $jezioraKategorium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jeziora_kategoria_show", methods={"GET"})
     */
    public function show(JezioraKategoria $jezioraKategorium): Response
    {
        return $this->render('admin/jeziora_kategoria/show.html.twig', [
            'jeziora_kategorium' => $jezioraKategorium,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="jeziora_kategoria_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, JezioraKategoria $jezioraKategorium): Response
    {
        $form = $this->createForm(JezioraKategoriaType::class, $jezioraKategorium);
        $form->handleRequest($request);

        $kategoria = strtoupper($jezioraKategorium->getKategoria());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "Pomyślnie zmodyfikowano kategorię -- {$kategoria} -- ");

            return $this->redirectToRoute('jeziora_kategoria_index');
        }

        return $this->render('admin/jeziora_kategoria/edit.html.twig', [
            'jeziora_kategorium' => $jezioraKategorium,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="jeziora_kategoria_delete", methods={"DELETE"})
     */
    public function delete(Request $request, JezioraKategoria $jezioraKategorium): Response
    {
        $kategoria = strtoupper($jezioraKategorium->getKategoria());

        if ($this->isCsrfTokenValid('delete'.$jezioraKategorium->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($jezioraKategorium);
            $entityManager->flush();

            $this->addFlash('success', "Pomyślnie usunięto kategorię -- {$kategoria} -- ");
        }

        return $this->redirectToRoute('jeziora_kategoria_index');
    }
}
