<?php

namespace App\Tests\Functionnal\Form;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FormContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Fomulaire de contact');

        //On recupere le formulaire
        $form = $crawler->selectButton("Soumettre")->form();
        //$this->assertEquals(1, count($form));

        //On rempli le formulaire
        $form['contact[fullName]'] = 'symfonyfan';
        $form['contact[email]'] = 'anypass';
        $form['contact[sujet]'] = 'anypass';
        $form['contact[description]'] = 'anypass';

        //Soumettre le formulaire
        $crawler = $client->submit($form);

        //Vérrifier le status code HTTP
        // $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Vérfier l'envoi du mail
        // $this->assertEmailCount(1);

        //Vérfiier le message de succées
        // $email = $this->getMailerMessage();
    }
}
