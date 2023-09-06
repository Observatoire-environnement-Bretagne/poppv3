<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LSerieAxeThematic
 *
 * @ORM\Table(name="l_serie_axe_thematic", indexes={@ORM\Index(name="fki_l_sat_axe_thematic_id", columns={"l_sat_axe_thematic_id"}), @ORM\Index(name="fki_l_sat_serie_id", columns={"l_sat_serie_id"})})
 * @ORM\Entity
 */
class LSerieAxeThematic
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_sat_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_serie_axe_thematic_l_sat_id_seq", allocationSize=1, initialValue=1)
     */
    private $lSatId;

    /**
     * @var \AxeThematic
     *
     * @ORM\ManyToOne(targetEntity="AxeThematic")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_sat_axe_thematic_id", referencedColumnName="axe_thematic_id")
     * })
     */
    private $lSatAxeThematic;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_sat_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $lSatSerie;

    public function getLSatId(): ?int
    {
        return $this->lSatId;
    }

    public function getLSatAxeThematic(): ?AxeThematic
    {
        return $this->lSatAxeThematic;
    }

    public function setLSatAxeThematic(?AxeThematic $lSatAxeThematic): self
    {
        $this->lSatAxeThematic = $lSatAxeThematic;

        return $this;
    }

    public function getLSatSerie(): ?Serie
    {
        return $this->lSatSerie;
    }

    public function setLSatSerie(?Serie $lSatSerie): self
    {
        $this->lSatSerie = $lSatSerie;

        return $this;
    }


}
