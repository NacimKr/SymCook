<?php

namespace App\Entity;

use App\Repository\RecettesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecettesRepository::class)]
class Recettes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(1)]
    private ?int $temps;

    #[ORM\Column(nullable: true)]
    #[Assert\LessThan(50)]
    private ?int $nb_personnes;

    #[ORM\Column(nullable: true)]
    #[Assert\GreaterThan(1)]
    #[Assert\LessThan(5)]
    private ?int $difficulty;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Assert\LessThan(1000)]
    private ?float $prix;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Ingredients::class)]
    private Collection $list_ingredients;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\OneToMany(mappedBy: 'recette', targetEntity: Notes::class)]
    private Collection $notes;

    private int $notesMoyennes;

    private int $noteSur = 5;

    public function __construct(){
        $this->createdat = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->list_ingredients = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTemps(): ?int
    {
        return $this->temps;
    }

    public function setTemps(int $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    public function getNbPersonnes(): ?int
    {
        return $this->nb_personnes;
    }

    public function setNbPersonnes(int $nb_personnes): self
    {
        $this->nb_personnes = $nb_personnes;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCreatedat(): ?\DateTimeImmutable
    {
        return $this->createdat;
    }

    public function setCreatedat(\DateTimeImmutable $createdat): self
    {
        $this->createdat = $createdat;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Ingredients>
     */
    public function getListIngredients(): Collection
    {
        return $this->list_ingredients;
    }

    public function addListIngredient(Ingredients $listIngredient): self
    {
        if (!$this->list_ingredients->contains($listIngredient)) {
            $this->list_ingredients->add($listIngredient);
        }

        return $this;
    }

    public function removeListIngredient(Ingredients $listIngredient): self
    {
        $this->list_ingredients->removeElement($listIngredient);

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

    public function isIsPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection<int, Notes>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Notes $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setRecette($this);
        }

        return $this;
    }

    public function removeNote(Notes $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getRecette() === $this) {
                $note->setRecette(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of notesMoyennes
     */ 
    public function getNotesMoyennes()
    {
        //On verifie si le tableau est vide ou non
        if(count($this->getNotes()) === 0){
            return 0;
        }
        $sum = 0;
        foreach($this->getNotes() as $notes){
            $sum += $notes->getNote();
        }
        
        // dump($this->getNotes());
        $this->notesMoyennes = $sum;

        return $this->notesMoyennes / count($this->getNotes());

    }

    /**
     * Get the value of noteSur
     */ 
    public function getNoteSur()
    {
        return $this->noteSur;
    }
}
