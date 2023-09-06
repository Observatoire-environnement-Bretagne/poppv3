<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Region
 *
 * @ORM\Table(name="region")
 * @ORM\Entity
 */
class Region
{
    /**
     * @var int
     *
     * @ORM\Column(name="region_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="region_region_id_seq", allocationSize=1, initialValue=1)
     */
    private $regionId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region_nom", type="string", length=255, nullable=true)
     */
    private $regionNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="region_desc", type="text", nullable=true)
     */
    private $regionDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="region_poids", type="integer", nullable=true)
     */
    private $regionPoids;

    public function getRegionId(): ?int
    {
        return $this->regionId;
    }

    public function getRegionNom(): ?string
    {
        return $this->regionNom;
    }

    public function setRegionNom(?string $regionNom): self
    {
        $this->regionNom = $regionNom;

        return $this;
    }

    public function getRegionDesc(): ?string
    {
        return $this->regionDesc;
    }

    public function setRegionDesc(?string $regionDesc): self
    {
        $this->regionDesc = $regionDesc;

        return $this;
    }

    public function getRegionPoids(): ?int
    {
        return $this->regionPoids;
    }

    public function setRegionPoids(?int $regionPoids): self
    {
        $this->regionPoids = $regionPoids;

        return $this;
    }


}
