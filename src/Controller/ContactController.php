<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\CategoryRepository;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();

        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            $contactFormData = $form->getData();

            
            $message = (new Email())
                ->from($contactFormData['email'])
                ->to('contact@shoyoup.com')
                ->subject('You got mail')
                ->text('Sender : '.$contactFormData['email'].\PHP_EOL.
                    $contactFormData['message'],
                    'text/plain');
            $mailer->send($message);




            $this->addFlash('success', 'Your message has been sent');

            return $this->redirectToRoute('contact');
        }



        return $this->render('contact/index.html.twig', [
            'contact_form' => $form->createView(),
            'categories' => $categories,
        ]);
    }
}
