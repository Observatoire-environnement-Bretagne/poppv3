<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AxeThematic
 *
 * @ORM\Table(name="axe_thematic")
 * @ORM\Entity
 */
class AxeThematic
{
    /**
     * @var int
     *
     * @ORM\Column(name="axe_thematic_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="axe_thematic_axe_thematic_id_seq", allocationSize=1, initialValue=1)
     */
    private $axeThematicId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="axe_thematic_nom", type="string", length=255, nullable=true)
     */
    private $axeThematicNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="axe_thematic_desc", type="text", nullable=true)
     */
    private $axeThematicDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="axe_thematic_poids", type="integer", nullable=true)
     */
    private $axeThematicPoids;

    public function getAxeThematicId(): ?int
    {
        return $this->axeThematicId;
    }

    public function getAxeThematicNom(): ?string
    {
        return $this->axeThematicNom;
    }

    public function setAxeThematicNom(?string $axeThematicNom): self
    {
        $this->axeThematicNom = $axeThematicNom;

        return $this;
    }

    public function getAxeThematicDesc(): ?string
    {
        return $this->axeThematicDesc;
    }

    public function setAxeThematicDesc(?string $axeThematicDesc): self
    {
        $this->axeThematicDesc = $axeThematicDesc;

        return $this;
    }

    public function getAxeThematicPoids(): ?int
    {
        return $this->axeThematicPoids;
    }

    public function setAxeThematicPoids(?int $axeThematicPoids): self
    {
        $this->axeThematicPoids = $axeThematicPoids;

        return $this;
    }


}
