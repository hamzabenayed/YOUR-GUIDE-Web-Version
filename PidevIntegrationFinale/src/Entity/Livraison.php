<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=LivraisonRepository::class)
 */
class Livraison implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomLivreur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomLivreur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telLivreur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresseLivraison;

    /**
     * @ORM\ManyToOne(targetEntity=Panier::class, inversedBy="livraisons")
     */
    private $panier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNomLivreur(): ?string
    {
        return $this->nomLivreur;
    }

    public function setNomLivreur(string $nomLivreur): self
    {
        $this->nomLivreur = $nomLivreur;

        return $this;
    }

    public function getPrenomLivreur(): ?string
    {
        return $this->prenomLivreur;
    }

    public function setPrenomLivreur(string $prenomLivreur): self
    {
        $this->prenomLivreur = $prenomLivreur;

        return $this;
    }

    public function getTelLivreur(): ?string
    {
        return $this->telLivreur;
    }

    public function setTelLivreur(string $telLivreur): self
    {
        $this->telLivreur = $telLivreur;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'date' => $this->date->format("d-m-Y"),
            'nomLivreur' => $this->nomLivreur,
            'prenomLivreur' => $this->prenomLivreur,
            'telLivreur' => $this->telLivreur,
            'adresseLivraison' => $this->adresseLivraison,
            'panier' => $this->panier
        );
    }

    public function setUp($date, $nomLivreur, $prenomLivreur, $telLivreur, $adresseLivraison, $panier)
    {
        $this->date = $date;
        $this->nomLivreur = $nomLivreur;
        $this->prenomLivreur = $prenomLivreur;
        $this->telLivreur = $telLivreur;
        $this->adresseLivraison = $adresseLivraison;
        $this->panier = $panier;
    }
}
