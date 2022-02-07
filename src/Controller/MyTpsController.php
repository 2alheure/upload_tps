<?php

namespace App\Controller;

use App\Entity\Render;
use App\Repository\RenderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @IsGranted("ROLE_USER")
 */
class MyTpsController extends AbstractController {
    /**
     * @Route("/my-tps/{id}", name="subject", methods={"GET"})
     */
    public function subject(Render $render): Response {
        if ($render->getDateBegin() > new \DateTime) throw new AccessDeniedHttpException('Vous ne pouvez pas consulter le sujet d\'un TP non commencé.');

        return $this->render('my_tps/subject.html.twig', [
            'render' => $render,
        ]);
    }

    /**
     * @Route("/my-tps", name="my_tps", methods={"GET"})
     */
    public function index(RenderRepository $renderRepository): Response {
        return $this->render('my_tps/index.html.twig', [
            'renders' => $renderRepository->findRendersOfUser($this->getUser()),
        ]);
    }
}
