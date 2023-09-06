<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UnitePaysage
 *
 * @ORM\Table(name="unite_paysage")
 * @ORM\Entity
 */
class UnitePaysage
{
    /**
     * @var int
     *
     * @ORM\Column(name="unite_paysage_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="unite_paysage_unite_paysage_id_seq", allocationSize=1, initialValue=1)
     */
    private $unitePaysageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unite_paysage_nom", type="string", length=255, nullable=true)
     */
    private $unitePaysageNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unite_paysage_desc", type="text", nullable=true)
     */
    private $unitePaysageDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="unite_paysage_poids", type="integer", nullable=true)
     */
    private $unitePaysagePoids;

    /**
     * @var \EnsemblePaysager
     *
     * @ORM\ManyToOne(targetEntity="EnsemblePaysager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unite_paysage_ensemble_id", referencedColumnName="ensemble_paysager_id")
     * })
     */
    private $unitePaysageEnsemble;

    public function getUnitePaysageId(): ?int
    {
        return $this->unitePaysageId;
    }

    public function getUnitePaysageNom(): ?string
    {
        return $this->unitePaysageNom;
    }

    public function setUnitePaysageNom(?string $unitePaysageNom): self
    {
        $this->unitePaysageNom = $unitePaysageNom;

        return $this;
    }

    public function getUnitePaysageDesc(): ?string
    {
        return $this->unitePaysageDesc;
    }

    public function setUnitePaysageDesc(?string $unitePaysageDesc): self
    {
        $this->unitePaysageDesc = $unitePaysageDesc;

        return $this;
    }

    public function getUnitePaysagePoids(): ?int
    {
        return $this->unitePaysagePoids;
    }

    public function setUnitePaysagePoids(?int $unitePaysagePoids): self
    {
        $this->unitePaysagePoids = $unitePaysagePoids;

        return $this;
    }

    public function getUnitePaysageEnsemble(): ?EnsemblePaysager
    {
        return $this->unitePaysageEnsemble;
    }

    public function setUnitePaysageEnsemble(?EnsemblePaysager $unitePaysageEnsemble): self
    {
        $this->unitePaysageEnsemble = $unitePaysageEnsemble;

        return $this;
    }
    

}
