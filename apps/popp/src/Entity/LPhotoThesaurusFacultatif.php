<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LPhotoThesaurusFacultatif
 *
 * @ORM\Table(name="l_photo_thesaurus_facultatif", indexes={@ORM\Index(name="fki_l_ptf_photo_id", columns={"l_ptf_photo_id"}), @ORM\Index(name="fki_l_ptf_thesaurus_id", columns={"l_ptf_thesaurus_id"})})
 * @ORM\Entity
 */
class LPhotoThesaurusFacultatif
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_ptf_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_photo_thesaurus_facultatif_l_ptf_id_seq", allocationSize=1, initialValue=1)
     */
    private $lPtId;

    /**
     * @var \Photo
     *
     * @ORM\ManyToOne(targetEntity="Photo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_ptf_photo_id", referencedColumnName="photo_id")
     * })
     */
    private $lPtfPhoto;

    /**
     * @var \ThesaurusTreeFacultatif
     *
     * @ORM\ManyToOne(targetEntity="ThesaurusTreeFacultatif")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_ptf_thesaurus_id", referencedColumnName="thesaurus_tree_facultatif_id")
     * })
     */
    private $lPtfThesaurus;

    public function getLPtId(): ?int
    {
        return $this->lPtId;
    }

    public function getLPtfPhoto(): ?Photo
    {
        return $this->lPtfPhoto;
    }

    public function setLPtfPhoto(?Photo $lPtfPhoto): self
    {
        $this->lPtfPhoto = $lPtfPhoto;

        return $this;
    }

    public function getLPtfThesaurus(): ?ThesaurusTreeFacultatif
    {
        return $this->lPtfThesaurus;
    }

    public function setLPtfThesaurus(?ThesaurusTreeFacultatif $lPtfThesaurus): self
    {
        $this->lPtfThesaurus = $lPtfThesaurus;

        return $this;
    }


}
