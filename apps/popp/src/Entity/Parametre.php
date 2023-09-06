<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParametreRepository")
 */
class Parametre
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
    private $prmCode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prmValeur;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $prmDesc;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrmCode(): ?string
    {
        return $this->prmCode;
    }

    public function setPrmCode(string $prmCode): self
    {
        $this->prmCode = $prmCode;

        return $this;
    }

    public function getPrmValeur(): ?string
    {
        return $this->prmValeur;
    }

    public function setPrmValeur(string $prmValeur): self
    {
        $this->prmValeur = $prmValeur;

        return $this;
    }

    public function getPrmDesc(): ?string
    {
        return $this->prmDesc;
    }

    public function setPrmDesc(?string $prmDesc): self
    {
        $this->prmDesc = $prmDesc;

        return $this;
    }
}
