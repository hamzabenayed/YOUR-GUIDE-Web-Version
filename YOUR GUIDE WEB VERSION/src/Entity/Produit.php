<?php

namespace App\Entity;

use JsonSerializable;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Le nom doit etre non vide")
     * @Assert\Type(type="alpha", message="Le nom ne doit pas contenir des chiffres .")
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Assert\NotBlank(message="Ecrivez quelques chose !")
     * @Assert\Length(
     *      min = 30,
     *      max = 1000,
     *      minMessage = "Description très courte ! ",
     *      maxMessage = "doit etre <=1000" )
     * @ORM\Column(type="string", length=1000)
     */
    private $description;
    /**
     * @Assert\NotBlank(message="Entrez quelques choses !")
     * @Assert\Positive(message="Le nombre de participants doit etre positif.")
     * @Assert\Range(
     *      min = 1,
     *      max = 8000,
     *      notInRangeMessage = "le prix doit etre valid",
     *     )
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category",cascade={"persist"}, inversedBy="products")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $category;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getProduitP(): ?Commande
    {
        return $this->produit_p;
    }

    public function setProduitP(?Commande $produit_p): self
    {
        $this->produit_p = $produit_p;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'nom' => $this->nom,//string
            'description' => $this->description,//string
            'prix' => $this->prix,//int
            'image' => $this->image,//string
            'category' => $this->category,//relation
        );
    }

    public function setUp($nom, $description, $prix, $image, $category)
    {
        $this->nom = $nom;
        $this->description = $description;
        $this->prix = $prix;
        $this->image = $image;
        $this->category = $category;
    }
}
