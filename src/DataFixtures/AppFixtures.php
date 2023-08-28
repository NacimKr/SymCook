<?php

namespace App\DataFixtures;

use App\Entity\Ingredients;
use App\Entity\Notes;
use App\Entity\Recettes;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher){
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        //User

        $users = []; 

        for($i=0; $i<10; $i++){
            $user = new User();
            $user->setEmail('test'.$i."@mail.com");

            //Pour ne plus faire ça on peut utiliser les entity listener afin d'eviter de le mettre dans le controller ou dans les fixtures 
            //Donc on exporte la logique du hashage de mot de passe dans un fichiers a part un peu comme un Service 'Entity Listener'
                // $hashedPassword = $this->passwordHasher->hashPassword($user,"test".$i);
                // $user->setPassword($hashedPassword);

            //Pour utiliser les entityListener on va utiliser le mot de passe en dur
            //Puis on va dans les service et on rajouter un autre service voir services.yaml
                $user->setPlainPassword("test");
                $user->setFullname('TestTest'.$i);
                //->setPseudo('PseudoTest'.$i)
                $user->setRoles(['ROLE_USER']);

                $users[] = $user;
 
            $manager->persist($user);
        }



        $ingredients = [];

        //Ingrédients
        for($i=0; $i<50; $i++){
            $ingredient = new Ingredients();
            $ingredient->setNom('ingredient'.$i)
                        ->setPrix(rand(1,1000))
                        ->setUser($users[rand(0,count($users)-1)]);

            $ingredients[] = $ingredient; 
            $manager->persist($ingredient);
        }

        //Recettes

        $recettes = [];

        for($j=0; $j<25; $j++){
            $recette = new Recettes();
            $recette->setNom('recette n°'.$j)
                    ->setTemps(mt_rand(1,1440))
                    ->setNbPersonnes(mt_rand(1,50))
                    ->setDifficulty(mt_rand(1,50))
                    ->setDescription("Lorem ipsum dolor sit amet n°".$j)
                    ->setPrix(mt_rand(1,10))
                    ->setIsPublic(rand(0,1) === 1 ? true : false)
                    ->setUser($users[rand(0, count($users) - 1)]);

            for($k=0; $k<rand(5,10); $k++){
                $recette->addListIngredient($ingredients[rand(0, count($ingredients) - 1)] );
            }

            $recettes[] = $recette;
            $manager->persist($recette);
        }



        for($i=0; $i<count($recettes); $i++){
            $notes = new Notes();
            $notes->setNote(rand(1,5))
                ->setRecette($recettes[rand(0, count($recettes) -1 )])
                ->setUser($users[rand(0, count($users)-1)])
            ;
            $manager->persist($notes);
        }

        $manager->flush();
    }
}
