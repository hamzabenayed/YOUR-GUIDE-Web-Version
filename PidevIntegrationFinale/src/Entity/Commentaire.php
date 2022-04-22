<?php

namespace App\Entity;

use DateTime;
use JsonSerializable;
use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank (message ="description is required")
     */
    private $description_commentaire;

    /**
     * @ORM\Column(type="date")
     */
    private $date_commentaire;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="commentaire", cascade={"all"}, orphanRemoval=true)
     */
    private $likes;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescriptionCommentaire(): ?string
    {
        return $this->description_commentaire;
    }

    public function setDescriptionCommentaire(string $description_commentaire): self
    {
        $this->description_commentaire = $description_commentaire;

        return $this;
    }

    public function getDateCommentaire(): ?string
    {
        return $this->date_commentaire;
    }

    public function setDateCommentaire(string $date_commentaire): self
    {
        $this->date_commentaire = $date_commentaire;

        return $this;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setCommentaire($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getCommentaire() === $this) {
                $like->setCommentaire(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'description' => $this->description_commentaire,//string
            'date' => $this->date_commentaire->format("d-m-Y"),
        );
    }

    public function setUp($description_commentaire)
    {
        $this->description_commentaire = $description_commentaire;
        $this->date_commentaire = new DateTime();
    }

}
