<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LienExterne
 *
 * @ORM\Table(name="lien_externe", indexes={@ORM\Index(name="fki_lien_externe_serie_id", columns={"lien_externe_serie_id"})})
 * @ORM\Entity
 */
class LienExterne
{
    /**
     * @var int
     *
     * @ORM\Column(name="lien_externe_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="lien_externe_lien_externe_id_seq", allocationSize=1, initialValue=1)
     */
    private $lienExterneId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lien_externe_value", type="string", length=255, nullable=true)
     */
    private $lienExterneValue;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lien_externe_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $lienExterneSerie;

    public function getLienExterneId(): ?int
    {
        return $this->lienExterneId;
    }

    public function getLienExterneValue(): ?string
    {
        return $this->lienExterneValue;
    }

    public function setLienExterneValue(?string $lienExterneValue): self
    {
        $this->lienExterneValue = $lienExterneValue;

        return $this;
    }

    public function getLienExterneSerie(): ?Serie
    {
        return $this->lienExterneSerie;
    }

    public function setLienExterneSerie(?Serie $lienExterneSerie): self
    {
        $this->lienExterneSerie = $lienExterneSerie;

        return $this;
    }


}
