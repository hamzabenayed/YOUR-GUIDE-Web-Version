<?php

namespace App\Entity;

use JsonSerializable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ReclamationRepository::class)
 */
class Reclamation implements JsonSerializable
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
    private $date_reclamation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type_reclamation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $desc_reclamation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReclamation(): ?string
    {
        return $this->date_reclamation;
    }

    public function setDateReclamation(string $date_reclamation): self
    {
        $this->date_reclamation = $date_reclamation;

        return $this;
    }

    public function getTypeReclamation(): ?string
    {
        return $this->type_reclamation;
    }

    public function setTypeReclamation(string $type_reclamation): self
    {
        $this->type_reclamation = $type_reclamation;

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


    public function getDescReclamation(): ?string
    {
        return $this->desc_reclamation;
    }

    public function setDescReclamation(string $desc_reclamation): self
    {
        $this->desc_reclamation = $desc_reclamation;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'date' => $this->date_reclamation->format("d-m-Y"),
            'type' => $this->type_reclamation,//string
            'etat' => $this->etat,//string
            'description' => $this->desc_reclamation,//string
        );
    }

    public function setUp($date_reclamation, $type_reclamation, $etat, $desc_reclamation)
    {
        $this->date_reclamation = $date_reclamation;
        $this->type_reclamation = $type_reclamation;
        $this->etat = $etat;
        $this->desc_reclamation = $desc_reclamation;
    }
}
