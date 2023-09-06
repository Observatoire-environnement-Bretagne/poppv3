<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EnsemblePaysager
 *
 * @ORM\Table(name="ensemble_paysager")
 * @ORM\Entity
 */
class EnsemblePaysager
{
    /**
     * @var int
     *
     * @ORM\Column(name="ensemble_paysager_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="ensemble_paysager_ensemble_paysager_id_seq", allocationSize=1, initialValue=1)
     */
    private $ensemblePaysagerId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ensemble_paysager_nom", type="string", length=255, nullable=true)
     */
    private $ensemblePaysagerNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ensemble_paysager_desc", type="text", nullable=true)
     */
    private $ensemblePaysagerDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ensemble_paysager_poids", type="integer", nullable=true)
     */
    private $ensemblePaysagerPoids;

    public function getEnsemblePaysagerId(): ?int
    {
        return $this->ensemblePaysagerId;
    }

    public function getEnsemblePaysagerNom(): ?string
    {
        return $this->ensemblePaysagerNom;
    }

    public function setEnsemblePaysagerNom(?string $ensemblePaysagerNom): self
    {
        $this->ensemblePaysagerNom = $ensemblePaysagerNom;

        return $this;
    }

    public function getEnsemblePaysagerDesc(): ?string
    {
        return $this->ensemblePaysagerDesc;
    }

    public function setEnsemblePaysagerDesc(?string $ensemblePaysagerDesc): self
    {
        $this->ensemblePaysagerDesc = $ensemblePaysagerDesc;

        return $this;
    }

    public function getEnsemblePaysagerPoids(): ?int
    {
        return $this->ensemblePaysagerPoids;
    }

    public function setEnsemblePaysagerPoids(?int $ensemblePaysagerPoids): self
    {
        $this->ensemblePaysagerPoids = $ensemblePaysagerPoids;

        return $this;
    }


}
