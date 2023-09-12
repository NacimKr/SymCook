<?php

namespace App\Tests\Functionnal;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;

class LoginFormTest extends WebTestCase
{
    public function testLoginForm(): void
    {
        $client = static::createClient();
        // $userRepository = static::getContainer()->get(UserRepository::class);

        // $testUser = $userRepository->findOneByEmail('test0@mail.com');

        // $client->loginUser($testUser);
        // $crawler = $client->request('GET', '/');
        // $this->assertResponseIsSuccessful();
        // $crawler->filter('.btn.btn-dark', 'Se déconnecter');

        //---> Best practices
        //Get route by UrlGeneratorInterface
        /** @var UrlGeneratorInterface */
        $urlGenerator = $client->getContainer()->get('router.default');
        $crawler = $client->request('GET', $urlGenerator->generate('security_login'));

        //Form
        $form = $crawler->filter("form[name='formLogin']")->form([
            "_username" => "test0@mail.com",
            "_password" => "test123"
        ]);

        $client->submit($form);


        //SIgnifie qu"on doit être rediiriger
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Pour simuler une redirection s'il yen a une
        $client->followRedirect();

        //Redirect to home with session
        $this->assertRouteSame('app_home');
    }


    public function testIsLoginFailsedWhenPasswordisWrong():void
    {
        $client = static::createClient();

        //On generer un url avec url generatorinterface
        $urlGenerator = static::getContainer()->get('router.default');
        
        //On fais une redirection sur l'url de login
        $crawler = $client->request("GET", $urlGenerator->generate('security_login'));

        //On rempli le formulaire en le selectionnant
        $form = $crawler->filter('form[name="formLogin"]')->form([
            "_username" => "test0@mail.com",
            "_password" => "test123_"
        ]);

        //On soumet le formulaire
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //On suit sa redirection
        $client->followRedirect();

        //On check s'il arrive bien a la page prévu mais comme c'est mal saisi on reste sur la meme page
        $this->assertRouteSame('security_login');

        // $invalid = $crawler->filter(".text-danger.text-center.mb-3");
        // $this->assertEquals(1, count($invalid));
        //si celui du haut fonctionne pas essayer celui d'en dessous
        $this->assertSelectorTextContains(".text-danger", "The presented password is invalid.");
    }
}
