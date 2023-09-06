<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commune
 *
 * @ORM\Table(name="commune")
 * @ORM\Entity
 */
class Commune
{
    /**
     * @var int
     *
     * @ORM\Column(name="commune_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="commune_commune_id_seq", allocationSize=1, initialValue=1)
     */
    private $communeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commune_nom", type="string", length=255, nullable=true)
     */
    private $communeNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commune_insee", type="string", length=255, nullable=true)
     */
    private $communeInsee;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commune_desc", type="text", nullable=true)
     */
    private $communeDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="commune_poids", type="integer", nullable=true)
     */
    private $communePoids;

    public function getCommuneId(): ?int
    {
        return $this->communeId;
    }

    public function getCommuneNom(): ?string
    {
        return $this->communeNom;
    }

    public function setCommuneNom(?string $communeNom): self
    {
        $this->communeNom = $communeNom;

        return $this;
    }

    public function getCommuneInsee(): ?string
    {
        return $this->communeInsee;
    }

    public function setCommuneInsee(?string $communeInsee): self
    {
        $this->communeInsee = $communeInsee;

        return $this;
    }

    public function getCommuneDesc(): ?string
    {
        return $this->communeDesc;
    }

    public function setCommuneDesc(?string $communeDesc): self
    {
        $this->communeDesc = $communeDesc;

        return $this;
    }

    public function getCommunePoids(): ?int
    {
        return $this->communePoids;
    }

    public function setCommunePoids(?int $communePoids): self
    {
        $this->communePoids = $communePoids;

        return $this;
    }


}
