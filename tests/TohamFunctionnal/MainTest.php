<?php

namespace App\Tests\TohamFunctionnal;

use App\Entity\Recettes;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainTest extends WebTestCase
{
    /**
     * This function provide Uri from provideUri function
     * @return void
     */
    public function testHome(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, "/");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        // $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('h2', 'Bienvenue sur SymRecipe');
    }

    public function testconnectRecipes()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', "/connexion");

        //On recupère le formulaire
        $form = $crawler->filter("form[name='formLogin']")->form([
            "_username" => "test0@mail.com",
            "_password" => "test"
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        
        //Pour simuler une redirection s'il yen a une
        $client->followRedirect();

        //Redirect to home with session
        $this->assertRouteSame('app_home');

        //La redirection vers la page des recettes une fois connecté
        $router = static::getContainer()->get('router.default');
        
        $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $entityManager->getRepository(Recettes::class)->findBy([]);
        $crawler = $client->request(Request::METHOD_GET, $router->generate('app_recettes'));

        // $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Liste des Recettes');
    }

    public function testIfCreateRecipeIsSuccessfull():void
    {
        $client = static::createClient();

        //On recupère notre generator et entityManager dans notre container de service
        $urlGenerator = static::getContainer()->get("router.default"); 
        $entityManager = static::getContainer()->get("doctrine.orm.default_entity_manager"); 

        // $user = $entityManager->find(User::class, 1);

        //On se rend sur la page de création de recettes
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_add'));
        $this->assertSelectorTextContains('h1', 'Ajouter une Recettes');

        $form = $crawler->selectButton('Valider')->form([
            "recettes[nom]" => "Recette1",
            "recettes[temps]" => 14,
            "recettes[nb_personnes]" => "Recette1",
            "recettes[difficulty]" => 3,
            "recettes[description]" => "Lorem ipsum dolor sit amet",
            "recettes[prix]" => 13
        ]);

        $client->submit($form);

        //On fait la redirection une la recette ajouter
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //on va la redirection qu'il nous propose
        $client->followRedirect();
    }
}
