<?php

namespace App\Controller;

use App\Entity\Promo;
use App\Entity\Render;
use App\Repository\PromoRepository;
use App\Repository\RenderRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * @Route("/my-tps", name="my_tps", methods={"GET", "POST"})
     */
    public function index(Request $request, RenderRepository $renderRepository, PromoRepository $promoRepository): Response {
        if ($this->IsGranted('ROLE_ADMIN')) {

            $form = $this->createFormBuilder()
                ->add('promo', EntityType::class, [
                    'label' => 'Promotion',
                    'class' => Promo::class,
                    'choice_label' => 'name',
                    'choice_value' => 'id'
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Valider'
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $renders = $renderRepository->findBy([
                    'promo' => $form->get('promo')->getData()
                ]);
            }

            $form = $form->createView();
        } else {
            $form = null;
        }

        if (empty($renders))
            $renders = $renderRepository->findRendersOfUser($this->getUser());

        return $this->render('my_tps/index.html.twig', [
            'renders' => $renders,
            'promoForm' => $form
        ]);
    }
}
