<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LDocumentAnnexeOpp
 *
 * @ORM\Table(name="l_document_annexe_opp", indexes={@ORM\Index(name="fki_l_dao_opp_id", columns={"l_dao_opp_id"}), @ORM\Index(name="fki_l_dao_document_annexe_id", columns={"l_dao_document_annexe_id"})})
 * @ORM\Entity
 */
class LDocumentAnnexeOpp
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_dao_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_document_annexe_opp_l_dao_id_seq", allocationSize=1, initialValue=1)
     */
    private $lDaoId;

    /**
     * @var \Opp
     *
     * @ORM\ManyToOne(targetEntity="Opp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_dao_opp_id", referencedColumnName="opp_id")
     * })
     */
    private $lDaoOpp;

    /**
     * @var \DocumentAnnexe
     *
     * @ORM\ManyToOne(targetEntity="DocumentAnnexe")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_dao_document_annexe_id", referencedColumnName="document_annexe_id")
     * })
     */
    private $lDaoDocumentAnnexe;

    public function getLDaoId(): ?int
    {
        return $this->lDaoId;
    }

    public function getLDaoOpp(): ?Opp
    {
        return $this->lDaoOpp;
    }

    public function setLDaoOpp(?Opp $lDaoOpp): self
    {
        $this->lDaoOpp = $lDaoOpp;

        return $this;
    }

    public function getLDaoDocumentAnnexe(): ?DocumentAnnexe
    {
        return $this->lDaoDocumentAnnexe;
    }

    public function setLDaoDocumentAnnexe(?DocumentAnnexe $lDaoDocumentAnnexe): self
    {
        $this->lDaoDocumentAnnexe = $lDaoDocumentAnnexe;

        return $this;
    }


}
