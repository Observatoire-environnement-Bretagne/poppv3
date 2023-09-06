<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Langue
 *
 * @ORM\Table(name="langue")
 * @ORM\Entity
 */
class Langue
{
    /**
     * @var int
     *
     * @ORM\Column(name="langue_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="langue_langue_id_seq", allocationSize=1, initialValue=1)
     */
    private $langueId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="langue_nom", type="string", length=255, nullable=true)
     */
    private $langueNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="langue_desc", type="text", nullable=true)
     */
    private $langueDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="langue_poids", type="integer", nullable=true)
     */
    private $languePoids;

    public function getLangueId(): ?int
    {
        return $this->langueId;
    }

    public function getLangueNom(): ?string
    {
        return $this->langueNom;
    }

    public function setLangueNom(?string $langueNom): self
    {
        $this->langueNom = $langueNom;

        return $this;
    }

    public function getLangueDesc(): ?string
    {
        return $this->langueDesc;
    }

    public function setLangueDesc(?string $langueDesc): self
    {
        $this->langueDesc = $langueDesc;

        return $this;
    }

    public function getLanguePoids(): ?int
    {
        return $this->languePoids;
    }

    public function setLanguePoids(?int $languePoids): self
    {
        $this->languePoids = $languePoids;

        return $this;
    }


}
