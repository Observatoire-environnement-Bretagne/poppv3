<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ThesaurusTreeFacultatif
 *
 * @ORM\Table(name="thesaurus_tree_facultatif")
 * @ORM\Entity
 */
class ThesaurusTreeFacultatif
{
    /**
     * @var int
     *
     * @ORM\Column(name="thesaurus_tree_facultatif_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="thesaurus_tree_facultatif_thesaurus_tree_facultatif_id_seq", allocationSize=1, initialValue=1)
     */
    private $thesaurusTreeFacultatifId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="thesaurus_tree_facultatif_nom", type="string", length=255, nullable=true)
     */
    private $thesaurusTreeFacultatifNom;

    /**
     * @var int|null
     *
     * @ORM\Column(name="thesaurus_tree_facultatif_parent_id", type="integer", nullable=true)
     */
    private $thesaurusTreeFacultatifParentId;

    public function getThesaurusTreeFacultatifId(): ?int
    {
        return $this->thesaurusTreeFacultatifId;
    }

    public function getThesaurusTreeFacultatifNom(): ?string
    {
        return $this->thesaurusTreeFacultatifNom;
    }

    public function setThesaurusTreeFacultatifNom(?string $thesaurusTreeFacultatifNom): self
    {
        $this->thesaurusTreeFacultatifNom = $thesaurusTreeFacultatifNom;

        return $this;
    }

    public function getThesaurusTreeFacultatifParentId(): ?int
    {
        return $this->thesaurusTreeFacultatifParentId;
    }

    public function setThesaurusTreeFacultatifParentId(?int $thesaurusTreeFacultatifParentId): self
    {
        $this->thesaurusTreeFacultatifParentId = $thesaurusTreeFacultatifParentId;

        return $this;
    }

}
