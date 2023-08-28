<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Entity\Recettes;
use App\Form\NoteType;
use App\Form\RecettesType;
use App\Repository\NotesRepository;
use App\Repository\RecettesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class RecettesController extends AbstractController
{
    #[Route('/recettes', name: 'app_recettes')]
    #[IsGranted('ROLE_USER')]
    public function index(RecettesRepository $recettesRepository,
    PaginatorInterface $paginator, Request $request): Response
    {
        //Pour recupèrere la liste des datas en fonction de l'utilisateur courant
        $recettes = $recettesRepository->findBy(["user" => $this->getUser()]);
        // dd($recettes);
        // dd($recettesRepository->findAll());

        $paginations = $paginator->paginate(
            $recettes, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            7 /*limit per page*/
        );

        return $this->renderForm('recettes/index.html.twig', [
            'paginations' => $paginations
        ]);
    }

    #[Route('recettes/public/', name:"app_recette_public")]
    // #[IsGranted("ROLE_USER")]
    public function indexPublic(RecettesRepository $recettesRepository){
        
        $recettes = $recettesRepository->showRecettes(true, 10);
        
        return $this->render('recettes/index_public.html.twig', [
            "recettes" => $recettes
        ]);
    }
    
    
    #[Route('/show/{id}', name:"app_show_recettes")]
    #[Security("is_granted('ROLE_USER') && recettes.getUser() === user")]
    public function publicRcetteShow(
        Recettes $recettes, 
        EntityManagerInterface $em, 
        Request $request,
        NotesRepository $notesRepository
    ){

        $notes = new Notes();
        $form = $this->createForm(NoteType::class, $notes);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $notes->setRecette($recettes); // -> on inserer la recettes qui est noté
            $notes->setUser($this->getUser()); // on inserer le user courant

            $existingNotes = $notesRepository->findOneBy([
                'user' => $this->getUser(),
                'recette' => $recettes
            ]);

            if(!$existingNotes){
                $em->persist($data);
                $em->flush();
                $this->addFlash('success', 'Votre note à bien été prise en compte');
                return $this->redirectToRoute('app_show_recettes', ['id' => $recettes->getId()]);
            }else{
                $this->addFlash('warning', 'Vous avez deja noter cette recette');
            }
        }

        return $this->render('recettes/show.html.twig', [
            "recettesShow" => $recettes,
            "form" => $form->createView()
        ]);
    }


    #[Route(path:"/add", name:"app_add")]
    public function add(Request $request, EntityManagerInterface $em){
        $recettes = new Recettes();

        $form = $this->createForm(RecettesType::class, $recettes);
        $form->handleRequest($request);

        //dd($recettes->getUser());

        if($form->isSubmitted() && $form->isValid()){
            $form->getData();

            //Quand on ajoute une data on le relie a l'utilisateur courant
            //Et aussi on ajoute un data dans la colonne user_id
            $recettes->setUser($this->getUser());
            $em->persist($recettes);
            $em->flush();
            return $this->redirectToRoute("app_recettes");
        }

        return $this->renderForm("add/formRecipe.html.twig",[
            "form" => $form
        ]);
    }

    #[Route('/delete/{id}', name:"delete_app")]
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $em, Recettes $recettes){
        $em->remove($recettes);
        $em->flush();
        return $this->redirectToRoute("app_recettes");
    }

    #[Route("/modify/{id}", name:"modify_recipe")]
    //#[Security("is_granted('ROLE_USER') and user === recettes.getUser()")]
    //Ou
    //#[Security("is_granted('ROLE_USER') and recettes.getUser() === user")]
    public function modify(EntityManagerInterface $em, 
    Request $request, Recettes $recettes){
        // dd($recettes);
        // die;

        $form = $this->createForm(RecettesType::class, $recettes);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $form->getData();
            $em->persist($recettes);
            $em->flush();
            return $this->redirectToRoute("app_recettes");
        }

        return $this->renderForm("add/formRecipe.html.twig",[
            "form" => $form
        ]);
    }
}
