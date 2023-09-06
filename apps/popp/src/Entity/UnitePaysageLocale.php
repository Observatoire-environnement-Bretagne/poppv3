<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UnitePaysageLocale
 *
 * @ORM\Table(name="unite_paysage_locale")
 * @ORM\Entity
 */
class UnitePaysageLocale
{
    /**
     * @var int
     *
     * @ORM\Column(name="unite_paysage_locale_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="unite_paysage_locale_unite_paysage_locale_id_seq", allocationSize=1, initialValue=1)
     */
    private $unitePaysageLocaleId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unite_paysage_locale_nom", type="string", length=255, nullable=true)
     */
    private $unitePaysageLocaleNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="unite_paysage_locale_desc", type="text", nullable=true)
     */
    private $unitePaysageLocaleDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="unite_paysage_locale_poids", type="integer", nullable=true)
     */
    private $unitePaysageLocalePoids;

    public function getUnitePaysageLocaleId(): ?int
    {
        return $this->unitePaysageLocaleId;
    }

    public function getUnitePaysageLocaleNom(): ?string
    {
        return $this->unitePaysageLocaleNom;
    }

    public function setUnitePaysageLocaleNom(?string $unitePaysageLocaleNom): self
    {
        $this->unitePaysageLocaleNom = $unitePaysageLocaleNom;

        return $this;
    }

    public function getUnitePaysageLocaleDesc(): ?string
    {
        return $this->unitePaysageLocaleDesc;
    }

    public function setUnitePaysageLocaleDesc(?string $unitePaysageLocaleDesc): self
    {
        $this->unitePaysageLocaleDesc = $unitePaysageLocaleDesc;

        return $this;
    }

    public function getUnitePaysageLocalePoids(): ?int
    {
        return $this->unitePaysageLocalePoids;
    }

    public function setUnitePaysageLocalePoids(?int $unitePaysageLocalePoids): self
    {
        $this->unitePaysageLocalePoids = $unitePaysageLocalePoids;

        return $this;
    }


}
