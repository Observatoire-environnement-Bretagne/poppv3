<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ThesaurusTree
 *
 * @ORM\Table(name="thesaurus_tree")
 * @ORM\Entity
 */
class ThesaurusTree
{
    /**
     * @var int
     *
     * @ORM\Column(name="thesaurus_tree_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="thesaurus_tree_thesaurus_tree_id_seq", allocationSize=1, initialValue=1)
     */
    private $thesaurusTreeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="thesaurus_tree_nom", type="string", length=255, nullable=true)
     */
    private $thesaurusTreeNom;

    /**
     * @var int|null
     *
     * @ORM\Column(name="thesaurus_tree_parent_id", type="integer", nullable=true)
     */
    private $thesaurusTreeParentId;

    public function getThesaurusTreeId(): ?int
    {
        return $this->thesaurusTreeId;
    }

    public function getThesaurusTreeNom(): ?string
    {
        return $this->thesaurusTreeNom;
    }

    public function setThesaurusTreeNom(?string $thesaurusTreeNom): self
    {
        $this->thesaurusTreeNom = $thesaurusTreeNom;

        return $this;
    }

    public function getThesaurusTreeParentId(): ?int
    {
        return $this->thesaurusTreeParentId;
    }

    public function setThesaurusTreeParentId(?int $thesaurusTreeParentId): self
    {
        $this->thesaurusTreeParentId = $thesaurusTreeParentId;

        return $this;
    }


}
