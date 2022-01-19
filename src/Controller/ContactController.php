<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController {
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, MailerInterface $mailer): Response {
        $formulaire = $this->createForm(ContactType::class, null, [
            'connected' => !empty($this->getUser()),
        ]);
        $formulaire->handleRequest($request);

        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $data = $formulaire->getData();

            if ($this->getUser()) {
                $sender = $this->getUser()->getFullName() . '<' . $this->getUser()->getEmail() . '>';
                $senderMail = $this->getUser()->getEmail();
                $senderPrint = $this->getUser()->getFullName() . ' ( Promotion ' . ($this->getUser()->getPromo() ? $this->getUser()->getPromo()->getName() : 'non définie') . ')';
            } else {
                $sender = $data['email'];
                $senderMail = $data['email'];
                $senderPrint = 'Un utilisateur du site';
            }

            $mailer->send(
                (new Email)
                    ->from($sender)
                    ->replyTo($sender)
                    ->to($_ENV['MAIL_CONTACT'])
                    ->subject($data['subject'])
                    ->text(
                        $senderPrint . ' vous a envoyé un message :' . PHP_EOL . PHP_EOL .
                            $data['message'] . PHP_EOL . PHP_EOL .
                            'Pour lui répondre, cliquez sur le bouton répondre ou bien écrivez à l\'adresse email suivante : ' . PHP_EOL .
                            $senderMail
                    )
            );

            $this->addFlash('success', 'Votre message a bien été envoyé.');
        }

        return $this->render('contact/index.html.twig', [
            'formulaire' => $formulaire->createView(),
        ]);
    }
}
