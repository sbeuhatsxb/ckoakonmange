<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LastUpdateRepository")
 */
class LastUpdate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastGlobalRefresh;

    public function getId()
    {
        return $this->id;
    }

    public function getLastGlobalRefresh(): ?\DateTimeInterface
    {
        return $this->lastGlobalRefresh;
    }

    public function setLastGlobalRefresh(?\DateTimeInterface $lastGlobalRefresh): self
    {
        $this->lastGlobalRefresh = $lastGlobalRefresh;

        return $this;
    }
}
