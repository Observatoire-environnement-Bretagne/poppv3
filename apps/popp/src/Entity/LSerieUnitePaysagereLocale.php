<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LSerieUnitePaysagereLocale
 *
 * @ORM\Table(name="l_serie_unite_paysagere_locale", indexes={@ORM\Index(name="fki_l_supl_unite_paysage_locale_id", columns={"l_supl_unite_paysage_locale_id"}), @ORM\Index(name="fki_l_supl_serie_id", columns={"l_supl_serie_id"})})
 * @ORM\Entity
 */
class LSerieUnitePaysagereLocale
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_supl_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_serie_unite_paysagere_locale_l_supl_id_seq", allocationSize=1, initialValue=1)
     */
    private $lSuplId;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_supl_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $lSuplSerie;

    /**
     * @var \UnitePaysageLocale
     *
     * @ORM\ManyToOne(targetEntity="UnitePaysageLocale")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_supl_unite_paysage_locale_id", referencedColumnName="unite_paysage_locale_id")
     * })
     */
    private $lSuplUnitePaysageLocale;

    public function getLSuplId(): ?int
    {
        return $this->lSuplId;
    }

    public function getLSuplSerie(): ?Serie
    {
        return $this->lSuplSerie;
    }

    public function setLSuplSerie(?Serie $lSuplSerie): self
    {
        $this->lSuplSerie = $lSuplSerie;

        return $this;
    }

    public function getLSuplUnitePaysageLocale(): ?UnitePaysageLocale
    {
        return $this->lSuplUnitePaysageLocale;
    }

    public function setLSuplUnitePaysageLocale(?UnitePaysageLocale $lSuplUnitePaysageLocale): self
    {
        $this->lSuplUnitePaysageLocale = $lSuplUnitePaysageLocale;

        return $this;
    }


}
