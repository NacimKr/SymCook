<?php

namespace App\Entity;

use App\Repository\NotesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NotesRepository::class)]
//On va raojouter le faites que une notes est unique sur la propriétés user et recettes
#[UniqueEntity(
    fields:['user', 'recette'], //-> chaque user et recdette devra être unique
    errorPath: "user", //-> pour dire que lerreur est lié à ce champs
    message: "Cet utilisateur a deja noté" //-> afficher le message en cas d'erreur
)]
class Notes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    private ?Recettes $recette = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at;

    public function __construct(){
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRecette(): ?Recettes
    {
        return $this->recette;
    }

    public function setRecette(?Recettes $recette): self
    {
        $this->recette = $recette;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
