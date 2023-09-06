<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Format
 *
 * @ORM\Table(name="format")
 * @ORM\Entity
 */
class Format
{
    /**
     * @var int
     *
     * @ORM\Column(name="format_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="format_format_id_seq", allocationSize=1, initialValue=1)
     */
    private $formatId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format_nom", type="string", length=255, nullable=true)
     */
    private $formatNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format_desc", type="text", nullable=true)
     */
    private $formatDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="format_poids", type="integer", nullable=true)
     */
    private $formatPoids;

    public function getFormatId(): ?int
    {
        return $this->formatId;
    }

    public function getFormatNom(): ?string
    {
        return $this->formatNom;
    }

    public function setFormatNom(?string $formatNom): self
    {
        $this->formatNom = $formatNom;

        return $this;
    }

    public function getFormatDesc(): ?string
    {
        return $this->formatDesc;
    }

    public function setFormatDesc(?string $formatDesc): self
    {
        $this->formatDesc = $formatDesc;

        return $this;
    }

    public function getFormatPoids(): ?int
    {
        return $this->formatPoids;
    }

    public function setFormatPoids(?int $formatPoids): self
    {
        $this->formatPoids = $formatPoids;

        return $this;
    }


}
