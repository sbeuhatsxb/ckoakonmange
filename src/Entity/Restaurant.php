<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RestaurantRepository")
 */
class Restaurant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $todaySpecial;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $veganTodaySpecial;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $lastUpdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mappy;

    public function getId()
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTodaySpecial(): ?string
    {
        return $this->todaySpecial;
    }

    public function setTodaySpecial(?string $todaySpecial): self
    {
        $this->todaySpecial = $todaySpecial;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVeganTodaySpecial(): ?string
    {
        return $this->veganTodaySpecial;
    }

    public function setVeganTodaySpecial(?string $veganTodaySpecial): self
    {
        $this->veganTodaySpecial = $veganTodaySpecial;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getMappy()
    {
        return $this->mappy;
    }

    public function setMappy($mappy): self
    {
        $this->mappy = $mappy;

        return $this;
    }
}
