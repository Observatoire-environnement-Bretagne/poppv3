<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LThesaurusEvolution
 *
 * @ORM\Table(name="l_thesaurus_evolution", indexes={@ORM\Index(name="fki_l_te_photo_thesaurus_id", columns={"l_te_photo_thesaurus_id"}), @ORM\Index(name="fki_l_te_evolution_id", columns={"l_te_evolution_id"})})
 * @ORM\Entity
 */
class LThesaurusEvolution
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_te_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_thesaurus_evolution_l_te_id_seq", allocationSize=1, initialValue=1)
     */
    private $lTeId;

    /**
     * @var \EvolutionPaysage
     *
     * @ORM\ManyToOne(targetEntity="EvolutionPaysage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_te_evolution_id", referencedColumnName="evolution_paysage_id")
     * })
     */
    private $lTeEvolution;

    /**
     * @var \LPhotoThesaurus
     *
     * @ORM\ManyToOne(targetEntity="LPhotoThesaurus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_te_photo_thesaurus_id", referencedColumnName="l_pt_id")
     * })
     */
    private $lTePhotoThesaurus;

    public function getLTeId(): ?int
    {
        return $this->lTeId;
    }

    public function getLTeEvolution(): ?EvolutionPaysage
    {
        return $this->lTeEvolution;
    }

    public function setLTeEvolution(?EvolutionPaysage $lTeEvolution): self
    {
        $this->lTeEvolution = $lTeEvolution;

        return $this;
    }

    public function getLTePhotoThesaurus(): ?LPhotoThesaurus
    {
        return $this->lTePhotoThesaurus;
    }

    public function setLTePhotoThesaurus(?LPhotoThesaurus $lTePhotoThesaurus): self
    {
        $this->lTePhotoThesaurus = $lTePhotoThesaurus;

        return $this;
    }


}
