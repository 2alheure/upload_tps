<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Entity\Render;
use App\Form\RenderType;
use App\Repository\RenderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/renders")
 * @IsGranted("ROLE_ADMIN")
 */
class RenderController extends AbstractController {
    /**
     * @Route("/", name="render_index", methods={"GET"})
     */
    public function index(RenderRepository $renderRepository): Response {
        return $this->render('render/index.html.twig', [
            'renders' => $renderRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{promo}", name="render_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, Promo $promo = null): Response {
        $render = new Render();
        if (!empty($promo)) $render->setPromo($promo);
        $form = $this->createForm(RenderType::class, $render);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($render);
            $entityManager->flush();

            return $this->redirectToRoute('promo_show', ['id' => $render->getPromo()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('render/new.html.twig', [
            'render' => $render,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="render_show", methods={"GET"})
     */
    public function show(Render $render): Response {
        return $this->render('render/show.html.twig', [
            'render' => $render,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="render_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Render $render, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(RenderType::class, $render);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('render_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('render/edit.html.twig', [
            'render' => $render,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="render_delete", methods={"POST"})
     */
    public function delete(Request $request, Render $render, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $render->getId(), $request->request->get('_token'))) {
            $entityManager->remove($render);
            $entityManager->flush();
        }

        return $this->redirectToRoute('render_index', [], Response::HTTP_SEE_OTHER);
    }
}
