<?php

namespace App\Tests\TohamFunctionnal;

use App\Entity\Recettes;
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

        $router = static::getContainer()->get('router.default');
        
        $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
        $recipes = $entityManager->getRepository(Recettes::class)->findBy([]);

        $crawler = $client->request(Request::METHOD_GET, $router->generate('app_recettes'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', "Liste des Recettes");
    }

    // public function testreadRecipes():void
    // {
    //     $client = static::createClient();

    //     $router = static::getContainer()->get('router.default');
        
    //     $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');
    //     $recipes = $entityManager->getRepository(Recettes::class)->findBy([]);

    //     $crawler = $client->request(Request::METHOD_GET, $router->generate('app_recettes'));

    //     $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    //     $this->assertSelectorTextContains('h1', "Liste des Recettes");
    // }
}
