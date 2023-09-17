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
    }

    


    public function testIfCreateRecipeIsSuccessfull():void
    {
        $client = static::createClient();

        //On recupère notre generator et entityManager dans notre container de service
        $urlGenerator = static::getContainer()->get("router.default"); 
        $entityManager = static::getContainer()->get("doctrine.orm.default_entity_manager"); 

        //On connecte le user
        $user = $entityManager->find(User::class, 1);
        $client->loginUser($user);

        // //On se rend sur la page de création de recettes
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('app_add'));
        $this->assertSelectorTextContains('h1', 'Ajouter une Recettes');

        $form = $crawler->filter("form[name='recettes']")->form([
            "recettes[nom]" => "Recette1",
            "recettes[temps]" => 14,
            "recettes[nb_personnes]" => 5,
            "recettes[difficulty]" => 3,
            "recettes[description]" => "Lorem ipsum dolor sit amet",
            "recettes[prix]" => 13
        ]);
        $client->submit($form);

        // // //On fait la redirection une fois la recette ajouter
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // // //on va la redirection qu'il nous propose
        $client->followRedirect();

        $this->assertRouteSame("app_recettes");
    }



    public function testReadRecipes():void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $entityManager = $container->get("doctrine.orm.default_entity_manager");
        $router = $container->get("router.default");
        $client->request(Request::METHOD_GET, $router->generate("app_recettes"));

        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);

        $crawler = $client->request(Request::METHOD_GET, $router->generate('app_recettes'));
        
        //On arrive bien dans notre redirection avec succès
        $this->assertResponseIsSuccessful();

        //On check si y'a bien le titre qu'on souhaite avoir
        $this->assertSelectorTextContains('h1', "Liste des Recettes");

        //On check si c'est bien notre route
        $this->assertRouteSame("app_recettes");
    }



    public function testUpdateRecipes():void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $router = $container->get('router.default');
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        //On recupère un utilisateur
        //On change on passe avec ne tableau de critéres et 
        //dans ce cas faudra utiliser la methode getRepository()
        $user = $entityManager->find(User::class, 1);

        //On checke que la recette est bien celle du user
        $recipes = $entityManager
            ->getRepository(Recettes::class)
            ->findOneBy([
                'user' => $user
            ]);

        //On loggue l'utilisateur
        $client->loginUser($user);

        //On se redirigie vers la page voulu (la page de modification des recettes)
        $crawler = $client->request(
            Request::METHOD_GET, 
            $router->generate('modify_recipe', ['id'=>1])
        );

        $form = $crawler->filter("form[name='recettes']")->form([
            "recettes[nom]" => "Recette",
            "recettes[temps]" => 14,
            "recettes[nb_personnes]" => 5,
            "recettes[difficulty]" => 3,
            "recettes[description]" => "Lorem ipsum dolor sit amet",
            "recettes[prix]" => 13
        ]);

        $client->submit($form);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }


    /**
     * pOUR CE QUE CE TEST FONCTIONNE J4AI DU MODIFIER LES CONSTRAINT AVEC LES NOTES CAR SINON ON SUPPRIME
     * LA RECETTE MAIS LES NOTES NON DU COUP ON A UNE 
     * ERROR CONSTRAINT KEY etc.
     */

    public function testDeleteRecipes():void
    {
        $client = static::createClient();
        $container = static::getContainer();

        $router = $container->get('router.default');
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        //On recupère un utilisateur
        //On change on passe avec ne tableau de critéres et 
        //dans ce cas faudra utiliser la methode getRepository()
        $user = $entityManager->find(User::class, 1);

        //On checke que la recette est bien celle du user
        $recipes = $entityManager
            ->getRepository(Recettes::class)
            ->findOneBy([
                'user' => $user
            ]);

        //On loggue l'utilisateur
        $client->loginUser($user);

        //On se redirigie vers la page voulu (la page de modification des recettes)
        $client->request(
            Request::METHOD_GET, 
            $router->generate('delete_app', ['id' => 1])
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
