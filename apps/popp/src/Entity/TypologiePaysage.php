<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypologiePaysage
 *
 * @ORM\Table(name="typologie_paysage")
 * @ORM\Entity
 */
class TypologiePaysage
{
    /**
     * @var int
     *
     * @ORM\Column(name="typologie_paysage_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="typologie_paysage_typologie_paysage_id_seq", allocationSize=1, initialValue=1)
     */
    private $typologiePaysageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typologie_paysage_nom", type="string", length=255, nullable=true)
     */
    private $typologiePaysageNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="typologie_paysage_desc", type="text", nullable=true)
     */
    private $typologiePaysageDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="typologie_paysage_poids", type="integer", nullable=true)
     */
    private $typologiePaysagePoids;

    public function getTypologiePaysageId(): ?int
    {
        return $this->typologiePaysageId;
    }

    public function getTypologiePaysageNom(): ?string
    {
        return $this->typologiePaysageNom;
    }

    public function setTypologiePaysageNom(?string $typologiePaysageNom): self
    {
        $this->typologiePaysageNom = $typologiePaysageNom;

        return $this;
    }

    public function getTypologiePaysageDesc(): ?string
    {
        return $this->typologiePaysageDesc;
    }

    public function setTypologiePaysageDesc(?string $typologiePaysageDesc): self
    {
        $this->typologiePaysageDesc = $typologiePaysageDesc;

        return $this;
    }

    public function getTypologiePaysagePoids(): ?int
    {
        return $this->typologiePaysagePoids;
    }

    public function setTypologiePaysagePoids(?int $typologiePaysagePoids): self
    {
        $this->typologiePaysagePoids = $typologiePaysagePoids;

        return $this;
    }


}
