<?php

namespace App\Tests\Functionnal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful(); // s'il correspond aux status code 200

        //On verifier qu'il y'a bien un boutton inscription en selection sa classe
        $buttonRegister = $crawler->filter('a[type="button"]');
        //var_dump($buttonRegister);
        //On checke si on a bien un boutton selectionner
        $this->assertEquals(1, count($buttonRegister));

        $this->assertSelectorTextContains('h2', 'Bienvenue sur SymRecipe');
    }
}
