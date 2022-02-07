<?php

namespace App\Controller;

use App\Entity\Render;
use App\Entity\Upload;
use App\Form\UploadType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/tp-renders")
 * @IsGranted("ROLE_USER")
 */
class UploadController extends AbstractController {
    /**
     * @Route("/new/{render}", name="upload_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Render $render): Response {
        if (!$render->isOpen()) throw new AccessDeniedHttpException('Impossible de faire un rendu pour un TP terminé');

        if (empty($upload = $render->getUploadOf($user = $this->getUser()))) {
            $upload = new Upload();
            $upload->setUser($user);
            $upload->setRender($render);
        }

        $form = $this->createForm(UploadType::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $renderFile = $form->get('render_file')->getData();
            if ($renderFile) {
                $renderFileName = $fileUploader->upload($renderFile, 'renders/' . $render->getPromo()->getName() . '/' . $render->getDirectory(), $user->getFullName());
                $upload->setRenderFile($renderFileName);
            }

            $entityManager->persist($upload);
            $entityManager->flush();

            return $this->redirectToRoute('my_tps', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('upload/new.html.twig', [
            'upload' => $upload,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="upload_show", methods={"GET"})
     */
    public function show(Upload $upload): Response {
        if ($upload->getUser()->getId() !== $this->getUser()->getId())
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('upload/show.html.twig', [
            'upload' => $upload,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="upload_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Upload $upload, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response {
        if (!$upload->getRender()->isOpen()) throw new AccessDeniedHttpException('Impossible de modifier un rendu pour un TP terminé');

        if ($upload->getUser()->getId() !== $this->getUser()->getId())
            $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UploadType::class, $upload, [
            'is_update' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $renderFile = $form->get('render_file')->getData();

            if ($renderFile) {
                $renderFileName = $fileUploader->upload(
                    $renderFile,
                    'renders',
                    $upload->getRenderFile() ?? ''
                );
                $upload->setRenderFile($renderFileName);
            } elseif ($form->get('drop_file')->getData() && $upload->getRenderFile()) {
                unlink($this->getParameter('targetDirectory') . '/' . $upload->getRenderFile());
                $upload->unsetRenderFile();
            }


            $entityManager->flush();

            return $this->redirectToRoute('my_tps', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('upload/edit.html.twig', [
            'upload' => $upload,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="upload_delete", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Upload $upload, EntityManagerInterface $entityManager): Response {
        if ($this->isCsrfTokenValid('delete' . $upload->getId(), $request->request->get('_token'))) {
            unlink($this->getParameter('targetDirectory') . '/' . $upload->getRenderFile());
            $entityManager->remove($upload);
            $entityManager->flush();
        }

        return $this->redirectToRoute('upload_index', [], Response::HTTP_SEE_OTHER);
    }
}
