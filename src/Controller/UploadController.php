<?php

namespace App\Controller;

use App\Entity\Render;
use App\Entity\Upload;
use App\Form\UploadType;
use App\Service\FileUploader;
use App\Repository\UploadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/upload")
 * @IsGranted("ROLE_USER")
 */
class UploadController extends AbstractController {
    /**
     * @Route("/", name="upload_index", methods={"GET"})
     */
    public function index(UploadRepository $uploadRepository): Response {
        return $this->render('upload/index.html.twig', [
            'uploads' => $this->getUser()->getUploads(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="upload_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, Render $render): Response {
        $upload = new Upload();
        $upload->setUser($this->getUser());
        $upload->setRender($render);

        $form = $this->createForm(UploadType::class, $upload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $renderFile = $form->get('render_file')->getData();
            if ($renderFile) {
                $renderFileName = $fileUploader->upload($renderFile, 'renders/' . $render->getPromo()->getName(), $this->getUser()->getFullName());
                $upload->setRenderFile($renderFileName);
            }

            $entityManager->persist($upload);
            $entityManager->flush();

            return $this->redirectToRoute('upload_index', [], Response::HTTP_SEE_OTHER);
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
            } elseif ($form->get('drop_file')->getData()) {
                unlink($this->getParameter('targetDirectory') . '/renders/' . $upload->getRender()->getPromo()->getName() . '/' . $upload->getRenderFile());
                $upload->unsetRenderFile();
            }


            $entityManager->flush();

            return $this->redirectToRoute('upload_index', [], Response::HTTP_SEE_OTHER);
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
            unlink($this->getParameter('targetDirectory') . '/renders/' . $upload->getRender()->getPromo()->getName() . '/' . $upload->getRenderFile());
            $entityManager->remove($upload);
            $entityManager->flush();
        }

        return $this->redirectToRoute('upload_index', [], Response::HTTP_SEE_OTHER);
    }
}
