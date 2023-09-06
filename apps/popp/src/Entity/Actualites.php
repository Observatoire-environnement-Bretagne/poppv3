<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actualites
 *
 * @ORM\Table(name="actualites")
 * @ORM\Entity
 */
class Actualites
{
    /**
     * @var int
     *
     * @ORM\Column(name="actualite_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="actualites_actualite_id_seq", allocationSize=1, initialValue=1)
     */
    private $actualiteId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="actualite_editor", type="text", nullable=true)
     */
    private $actualiteEditor;

    /**
     * @var int|null
     *
     * @ORM\Column(name="actualite_ordre", type="integer", nullable=true)
     */
    private $actualiteOrdre;

    public function getActualiteId(): ?int
    {
        return $this->actualiteId;
    }

    public function getActualiteEditor(): ?string
    {
        return $this->actualiteEditor;
    }

    public function setActualiteEditor(?string $actualiteEditor): self
    {
        $this->actualiteEditor = $actualiteEditor;

        return $this;
    }

    public function getActualiteOrdre(): ?int
    {
        return $this->actualiteOrdre;
    }

    public function setActualiteOrdre(?int $actualiteOrdre): self
    {
        $this->actualiteOrdre = $actualiteOrdre;

        return $this;
    }


}
