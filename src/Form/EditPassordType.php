<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EditPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add("plainPassword", RepeatedType::class, [
                "type" => PasswordType::class,
                "required" => true,
                'first_options'  => ['label' => 'Votre mot de passe'],
                'second_options' => ['label' => 'Confirmer votre mot de passe']
            ])
            ->add('newPassword', PasswordType::class, [
                "required" => true,
                "label" => "Nouveau mot de passe",
                "label_attr" => [
                    "class" => "mt-5"
                ],
            ])
            ->add('submit', SubmitType::class, [
                "label" => "Changer mon mot de passe",
                "attr" => [
                    "class" => "mt-2 btn btn-primary"
                ]
            ])
            ;
    }

}