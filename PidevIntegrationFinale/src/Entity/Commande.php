<?php

namespace App\Entity;

use DateTimeInterface;
use JsonSerializable;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date", length=255)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $commentaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $totalcost;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $product;

    public function __construct()
    {
        $this->commande_produit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTotalcost(): ?string
    {
        return $this->totalcost;
    }

    public function setTotalcost(string $totalcost): self
    {
        $this->totalcost = $totalcost;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|produit[]
     */
    public function getCommandeProduit(): Collection
    {
        return $this->commande_produit;
    }

    public function addCommandeProduit(produit $commandeProduit): self
    {
        if (!$this->commande_produit->contains($commandeProduit)) {
            $this->commande_produit[] = $commandeProduit;
            $commandeProduit->setProduitP($this);
        }

        return $this;
    }

    public function removeCommandeProduit(produit $commandeProduit): self
    {
        if ($this->commande_produit->removeElement($commandeProduit)) {
            // set the owning side to null (unless already changed)
            if ($commandeProduit->getProduitP() === $this) {
                $commandeProduit->setProduitP(null);
            }
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->id,
            'date' => $this->date->format("d-m-Y"),
            'etat' => $this->etat,//String
            'commentaire' => $this->commentaire,//String
            'adresse' => $this->adresse,//String
            'totalcost' => $this->totalcost,//String
            'product' => $this->product,//String
        );
    }

    public function setUp($date, $etat, $commentaire, $adresse, $totalcost, $product)
    {
        $this->date = $date;
        $this->etat = $etat;
        $this->commentaire = $commentaire;
        $this->adresse = $adresse;
        $this->totalcost = $totalcost;
        $this->product = $product;
    }
}
