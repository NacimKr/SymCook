<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService {
    
    public function __construct(private MailerInterface $mailer){
        $this->mailer = $mailer;
    }

    public function sendEmailContact(
        string $email, 
        string $subject, 
        string $description
    ):void
    {
        //On envoie l'email après le contact créer dans la base de donnée
        $email = (new Email())
        //On precise l'email de celui qui nous a envoeyer le contact
        ->from($email)
        ->to('admin@symrecipe.com')
        ->subject($subject)
        ->text('Sending emails is fun again!')
        ->html('<p>'.$description.'</p>');
        $this->mailer->send($email);
        
    }
}