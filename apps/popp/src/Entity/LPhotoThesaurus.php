<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LPhotoThesaurus
 *
 * @ORM\Table(name="l_photo_thesaurus", indexes={@ORM\Index(name="fki_l_pt_photo_id", columns={"l_pt_photo_id"}), @ORM\Index(name="fki_l_pt_thesaurus_id", columns={"l_pt_thesaurus_id"})})
 * @ORM\Entity
 */
class LPhotoThesaurus
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_pt_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_photo_thesaurus_l_pt_id_seq", allocationSize=1, initialValue=1)
     */
    private $lPtId;

    /**
     * @var \Photo
     *
     * @ORM\ManyToOne(targetEntity="Photo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_pt_photo_id", referencedColumnName="photo_id")
     * })
     */
    private $lPtPhoto;

    /**
     * @var \ThesaurusTree
     *
     * @ORM\ManyToOne(targetEntity="ThesaurusTree")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_pt_thesaurus_id", referencedColumnName="thesaurus_tree_id")
     * })
     */
    private $lPtThesaurus;

    public function getLPtId(): ?int
    {
        return $this->lPtId;
    }

    public function getLPtPhoto(): ?Photo
    {
        return $this->lPtPhoto;
    }

    public function setLPtPhoto(?Photo $lPtPhoto): self
    {
        $this->lPtPhoto = $lPtPhoto;

        return $this;
    }

    public function getLPtThesaurus(): ?ThesaurusTree
    {
        return $this->lPtThesaurus;
    }

    public function setLPtThesaurus(?ThesaurusTree $lPtThesaurus): self
    {
        $this->lPtThesaurus = $lPtThesaurus;

        return $this;
    }


}
