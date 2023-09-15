<?php

namespace App\Form;

use App\Entity\Ingredients;
use App\Entity\Recettes;
use App\Repository\IngredientsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecettesType extends AbstractType
{

    private TokenStorageInterface $token;

    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('temps', IntegerType::class)
            ->add('nb_personnes',IntegerType::class)
            ->add('difficulty',RangeType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('prix',IntegerType::class)
            ->add("list_ingredients", EntityType::class, [
                'class' => Ingredients::class,
                // 'query_builder' => function(IngredientsRepository $ir){
                //     return $ir->createQueryBuilder('i')
                //     ->where('i.user_id = :user')
                //     ->setParameter('user', $this->token->getToken()->getUser())
                //     ->orderBy('i.nom', 'ASC');
                // },
                "choice_label" => "nom",
                'multiple' => true,
                'expanded' => true,
                "label" => "Les ingrédients qu'il faut :"
            ])
            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recettes::class,
        ]);
    }
}
