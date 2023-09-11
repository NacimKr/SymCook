<?php

namespace App\Tests\Kernel;

use App\Entity\Notes;
use App\Entity\Recettes;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityTest extends KernelTestCase
{
    private $container;

    public function setUp():void
    {
        self::bootKernel();
        $this->container = static::getContainer();
    }

    public function getEntityRecettes():Recettes
    {
        $recipes = new Recettes();
        $recipes->setNom("Recipe #1")
                ->setTemps(rand(10,20))
                ->setNbPersonnes(rand(10,49))
                ->setDifficulty(rand(2,4))
                ->setPrix(rand(20,400))
                ->setDescription("Lorem ispum dolor sit amet")
                ->setCreatedat(new \DateTimeImmutable())
                ->setUpdatedAt(new \DateTimeImmutable())
        ;
        return $recipes;
    }

    public function testEntityRecetteisValid(): void
    {
        $recipes = $this->getEntityRecettes();
        $errors = $this->container->get('validator')->validate($recipes);
        $this->assertCount(0, $errors);
    }

    public function testEntityRecetteIsNotValid():void
    {
        $recipes = $this->getEntityRecettes();
        $recipes->setNbPersonnes(rand(50,60));
        $errors = $this->container->get('validator')->validate($recipes);
        $this->assertCount(1, $errors);
    }

    public function testAverageRecetteIsCorrect():void
    {
        $recipes = $this->getEntityRecettes();

        $user = $this->container->get(User::class)->findAll();
        print_r($user);

        $notes = new Notes();
        $notes->setNote(5)
              ->setRecette($recipes)
              ->setUser($$user)
        ;
        $averageRecipes = $recipes->getNotesMoyennes();

        $this->assertTrue($averageRecipes !== 5);
    }
}
