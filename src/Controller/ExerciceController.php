<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Service\FileUploader;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/exercices")
 * @IsGranted("ROLE_ADMIN")
 */
class ExerciceController extends AbstractController {
    /**
     * @Route("/", name="exercice_index", methods={"GET"})
     */
    public function index(ExerciceRepository $exerciceRepository): Response {
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="exercice_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Module $module = null): Response {
        $exercice = new Exercice();
        if (!empty($module)) $exercice->setModule($module);
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subjectFile = $form->get('subject_file')->getData();
            if ($subjectFile) {
                $subjectFileName = $fileUploader->upload($subjectFile, 'subjects');
                $exercice->setSubjectFile($subjectFileName);
            }

            $entityManager->persist($exercice);
            $entityManager->flush();

            return $this->redirectToRoute('module_show', ['id' => $exercice->getModule()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_show", methods={"GET"})
     */
    public function show(Exercice $exercice): Response {
        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="exercice_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Exercice $exercice, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response {
        $form = $this->createForm(ExerciceType::class, $exercice, [
            'is_update' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subjectFile = $form->get('subject_file')->getData();

            if ($subjectFile) {
                $subjectFileName = $fileUploader->upload(
                    $subjectFile,
                    'subjects',
                    $exercice->getSubjectFile() ?? ''
                );
                $exercice->setSubjectFile($subjectFileName);
            } elseif ($form->get('drop_file')->getData()) {
                unlink($this->getParameter('targetDirectory') . '/subjects/' . $exercice->getSubjectFile());
                $exercice->unsetSubjectFile();
            }

            $entityManager->flush();

            return $this->redirectToRoute('exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_delete", methods={"POST"})
     */
    public function delete(Request $request, Exercice $exercice, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $exercice->getId(), $request->request->get('_token'))) {
            unlink($this->getParameter('targetDirectory') . '/subjects/' . $exercice->getSubjectFile());
            $entityManager->remove($exercice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('exercice_index', [], Response::HTTP_SEE_OTHER);
    }
}
