<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LThesaurusFacultatifEvolution
 *
 * @ORM\Table(name="l_thesaurus_facultatif_evolution", indexes={@ORM\Index(name="fki_l_tfe_photo_thesaurus_id", columns={"l_tfe_photo_thesaurus_id"}), @ORM\Index(name="fki_l_tfe_evolution_id", columns={"l_tfe_evolution_id"})})
 * @ORM\Entity
 */
class LThesaurusFacultatifEvolution
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_tfe_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_thesaurus_facultatif_evolution_l_tfe_id_seq", allocationSize=1, initialValue=1)
     */
    private $lTfeId;

    /**
     * @var \EvolutionPaysage
     *
     * @ORM\ManyToOne(targetEntity="EvolutionPaysage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_tfe_evolution_id", referencedColumnName="evolution_paysage_id")
     * })
     */
    private $lTfeEvolution;

    /**
     * @var \LPhotoThesaurusFacultatif
     *
     * @ORM\ManyToOne(targetEntity="LPhotoThesaurusFacultatif")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_tfe_photo_thesaurus_id", referencedColumnName="l_ptf_id")
     * })
     */
    private $lTfePhotoThesaurus;

    public function getLTfeId(): ?int
    {
        return $this->lTfeId;
    }

    public function getLTfeEvolution(): ?EvolutionPaysage
    {
        return $this->lTfeEvolution;
    }

    public function setLTfeEvolution(?EvolutionPaysage $lTfeEvolution): self
    {
        $this->lTfeEvolution = $lTfeEvolution;

        return $this;
    }

    public function getLTfePhotoThesaurus(): ?LPhotoThesaurusFacultatif
    {
        return $this->lTfePhotoThesaurus;
    }

    public function setLTfePhotoThesaurus(?LPhotoThesaurusFacultatif $lTfePhotoThesaurus): self
    {
        $this->lTfePhotoThesaurus = $lTfePhotoThesaurus;

        return $this;
    }


}
