<?php

namespace App\Controller;

use App\Entity\Recettes;
use App\Entity\User;
use App\Form\EditPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * This controller allow us to edit user's profile
     */
    #[Route('/edit/user/{id}', name: 'app_user_edit')]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function index(
        User $choosenUser, 
        Request $request, 
        EntityManagerInterface $em, 
        UserPasswordHasherInterface $userPassword ): Response
    {


        //On a commenter ces ligne car on gérer l'accés a ces pages grace au compasant d'accées au pages

        // //Si l'utilisateur est PAS connecté
        // if(!$this->getUser()){
        //     return $this->redirectToRoute('security_login');
        // }

        // //Si l'utilisateur courant n'est pas celui avec l'id qu'on a recupèrer au moment de la connexion
        // elseif($this->getUser() !== $user){
        //     return $this->redirectToRoute('app_recettes');
        // }

        $formEditUser = $this->createForm(UserType::class, $choosenUser);
        $formEditUser->handleRequest($request);

        if($formEditUser->isSubmitted() && $formEditUser->isValid()){
            //On verifie sir le mot de passe est le meme que celui en base de donnée alors on autyorise la modification des données
            if($userPassword->isPasswordValid($choosenUser, $formEditUser->getData()->getPlainPassword())){
                $data = $formEditUser->getData();
                $this->addFlash(
                    'notice',
                    'Votre compte utilisateur a bien été modifié'
                );
                $em->persist($data);
                $em->flush();
    
                return $this->redirectToRoute('app_recettes'); 
            }else{
                $this->addFlash(
                    'notice',
                    'Le mot de passe est incorrect'
                );
            }
        }

        return $this->render('user/editUser.html.twig', [
            'formEditUser' => $formEditUser->createView()
        ]);
    }

    #[Route('/edit/password/{id}', name:"app_edit_password")]
    #[Security("is_granted('ROLE_USER') and user === choosenUser")]
    public function editPassword(
    User $choosenUser, Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $hasher){

        // //Si utilisateur n'est pas connecter 
        // //on le recupere avec le getUser()
        // if(!$this->getUser()){
        //     return $this->redirectToRoute('security_login');
        // }

        // //Si l'utilisateur est celui qui posséder cette id
        // //passé en paramètre
        // if($this->getUser() !== $choosenUser){
        //     return $this->redirectToRoute('app_home');
        // }
        
        $formEditPassword = $this->createForm(EditPasswordType::class);

        $formEditPassword->handleRequest($request);
        //dd($formEditPassword->getData());
        if ($formEditPassword->isSubmitted() && $formEditPassword->isValid()){
            //On verifie sir le mot de passe est le meme que celui en base de donnée 
            //alors on autorise la modification des données
            
            //dd($formEditPassword->getData()['plainPassword']);
            
            if($hasher->isPasswordValid($choosenUser, $formEditPassword->getData()['plainPassword'])){
                
                //on prend le setter pouvoir mettre a jour le mot de passe
                $choosenUser->setPassword(
                    $hasher->hashPassword(
                        $choosenUser,
                        $formEditPassword->getData()['newPassword']
                    )
                );

                $this->addFlash(
                    'notice',
                    'Votre mot de passe a bien été modifié'
                );

                $em->persist($choosenUser);
                $em->flush();
                
                //dd($formEditPassword->getData());
                return $this->redirectToRoute('app_recettes'); 
            }else{
                $this->addFlash(
                    'notice',
                    'Le mot de passe est incorrect'
                );
            }
            
        }
        
        return $this->render('user/editPassword.html.twig',[
            "formEditPassword" => $formEditPassword->createView()
        ]);
    
    }
}
