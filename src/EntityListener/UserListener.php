<?php

namespace App\EntityListener;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserListener{

    private UserPasswordHasherInterface $hashedPassword;

    public function __construct(UserPasswordHasherInterface $hashedPassword){
        $this->hashedPassword = $hashedPassword;
    }

    public function prePersist(User $user){
        $this->encodePassword($user);
    }

    public function update(User $user){
        $this->encodePassword($user);
    }

    /**
     * This function it's create for encode Password in DataBase
     *
     * @param User $user
     * @return void
     */
    public function encodePassword(User $user){
        //On verifie s'il est vide
        if($user->getPlainPassword() === null){
            return;
        }

        $hashPassword = $this->hashedPassword->hashPassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($hashPassword);
    }

}