<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security_login', methods:['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
       $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
       $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/deconnexion', name: 'security_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[Route('/inscription', name:"security_inscription")]
    public function register(Request $request, EntityManagerInterface $em){

        $user = new User();
        $formRegister = $this->createForm(RegistrationType::class, $user);

        $formRegister->handleRequest($request);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            $user = $formRegister->getData();
            $this->addFlash(
                'notice',
                'Votre compte utilisateur a bien été créé'
            );
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/register.html.twig',[
            "formRegister" => $formRegister->createView()
        ]);
    }
}