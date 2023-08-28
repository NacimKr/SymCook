<?php
namespace App\Controller;

use App\Entity\Ingredients;
use App\Form\IngredientsType;
use App\Repository\IngredientsRepository;
use App\Repository\RecettesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class MainController extends AbstractController
{
    #[Route(path:"/", name:"app_home")]
    public function index(RecettesRepository $recettesRepository): Response
    {

        $recettes = $recettesRepository->showRecettes(true, 3);

        return $this->render('home/home.html.twig',[
            "recettes" => $recettes
        ]);
    }

    #[Route(path:'/list_ingredient', name:"app_list", methods:['GET','POST'])]
    #[IsGranted('ROLE_USER')]
    public function list(
        IngredientsRepository $repositoryIngredients,
        PaginatorInterface $paginator,
        Request $request
    ) :Response
    {
        //On recupère les datas en fonction de l'utilisateur courant avec la méthode $this->getUser();
        $ingredients = $repositoryIngredients->findBy(['user' => $this->getUser()]);
        //$ingredients = $repositoryIngredients->findAll();

        $pagination = $paginator->paginate(
            $ingredients,
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );

        return $this->render('list/list.html.twig',[
            "ingredients" => $pagination
        ]);
    }

    #[Route('/ajouter', name:"add_recipe")]
    #[IsGranted('ROLE_USER')]
    public function ajouter(Request $request, EntityManagerInterface $em){

        $ingredient = new Ingredients();

        $form = $this->createForm(IngredientsType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();

            //Pour lier la creation de l'ingredient à utilisateur en question
            $ingredient->setUser($this->getUser());
            
            $em->persist($task);
            $em->flush();
            //dd($task);
            return $this->redirectToRoute('app_list');
        }

        return $this->renderForm('add/form.html.twig', [
            "form" => $form
        ]);
    }

    #[Route('/supprimer/{id}', name:"delete_app")]
    #[IsGranted('ROLE_USER')]
    public function delete( EntityManagerInterface $em, Ingredients $ingredients){
        $em->remove($ingredients);
        $em->flush();
        return $this->redirectToRoute('app_list');
    }

    #[Route('/modifier/{id}', name:"modify_ingredients")]
    //Pour le and on prend l'entité puis sont accesseur commençant par get... et
    //on le compare a une entité
    #[Security("is_granted('ROLE_USER') and user.getIngredients() === ingredient")]
    //Ou
    //#[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    public function modifier(Request $request, 
    EntityManagerInterface $em, Ingredients $ingredient
    ){
        $form = $this->createForm(IngredientsType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $em->persist($task);
            $em->flush();
            //dd($task);
            return $this->redirectToRoute('app_list');
        }

        return $this->renderForm('add/form.html.twig', [
            "form" => $form
        ]);
    }

}