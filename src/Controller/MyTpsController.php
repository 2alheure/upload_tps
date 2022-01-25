<?php

namespace App\Controller;

use App\Repository\RenderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @IsGranted("ROLE_USER")
 */
class MyTpsController extends AbstractController {
    /**
     * @Route("/my-tps", name="my_tps", methods={"GET"})
     */
    public function index(RenderRepository $renderRepository): Response {
        return $this->render('my_tps/index.html.twig', [
            'renders' => $renderRepository->findRendersOfUser($this->getUser()),
        ]);
    }
}
