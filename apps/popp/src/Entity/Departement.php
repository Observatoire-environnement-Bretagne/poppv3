<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Departement
 *
 * @ORM\Table(name="departement")
 * @ORM\Entity
 */
class Departement
{
    /**
     * @var int
     *
     * @ORM\Column(name="departement_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="departement_departement_id_seq", allocationSize=1, initialValue=1)
     */
    private $departementId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="departement_nom", type="string", length=255, nullable=true)
     */
    private $departementNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="departement_code", type="string", length=255, nullable=true)
     */
    private $departementCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="departement_desc", type="text", nullable=true)
     */
    private $departementDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="departement_poids", type="integer", nullable=true)
     */
    private $departementPoids;

    public function getDepartementId(): ?int
    {
        return $this->departementId;
    }

    public function getDepartementNom(): ?string
    {
        return $this->departementNom;
    }

    public function setDepartementNom(?string $departementNom): self
    {
        $this->departementNom = $departementNom;

        return $this;
    }

    public function getDepartementCode(): ?string
    {
        return $this->departementCode;
    }

    public function setDepartementCode(?string $departementCode): self
    {
        $this->departementCode = $departementCode;

        return $this;
    }

    public function getDepartementDesc(): ?string
    {
        return $this->departementDesc;
    }

    public function setDepartementDesc(?string $departementDesc): self
    {
        $this->departementDesc = $departementDesc;

        return $this;
    }

    public function getDepartementPoids(): ?int
    {
        return $this->departementPoids;
    }

    public function setDepartementPoids(?int $departementPoids): self
    {
        $this->departementPoids = $departementPoids;

        return $this;
    }


}
