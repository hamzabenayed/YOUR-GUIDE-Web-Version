<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use App\Repository\PromoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PromoRepository::class)
 */
class Promo implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pourcentage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Codepromo::class, inversedBy="promos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $codepromo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCodepromo(): ?Codepromo
    {
        return $this->codepromo;
    }

    public function setCodepromo(?Codepromo $codepromo): self
    {
        $this->codepromo = $codepromo;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id,
            'pourcentage' => $this->pourcentage, //float
            'codepromo' => $this->codepromo, //relation
            'image' => $this->image, //string
        );
    }

    public function setUp($pourcentage, $codepromo, $image)
    {
        $this->pourcentage = $pourcentage;
        $this->codepromo = $codepromo;
        $this->image = $image;
    }

}
