<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentAnnexe
 *
 * @ORM\Table(name="document_annexe", indexes={@ORM\Index(name="fki_document_annexe_file_id", columns={"document_annexe_file_id"}), @ORM\Index(name="fki_document_annexe_opp_id", columns={"document_annexe_opp_id"})})
 * @ORM\Entity
 */
class DocumentAnnexe
{
    /**
     * @var int
     *
     * @ORM\Column(name="document_annexe_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="document_annexe_document_annexe_id_seq", allocationSize=1, initialValue=1)
     */
    private $documentAnnexeId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_annexe_titre", type="string", length=255, nullable=true)
     */
    private $documentAnnexeTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_annexe_desc", type="text", nullable=true)
     */
    private $documentAnnexeDesc;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="document_annexe_all_opp ", type="boolean", nullable=true)
     */
    private $documentAnnexeAllOpp  = false;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_annexe_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $documentAnnexeFile;

    /**
     * @var \Opp
     *
     * @ORM\ManyToOne(targetEntity="Opp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_annexe_opp_id", referencedColumnName="opp_id")
     * })
     */
    private $documentAnnexeOpp;

    public function getDocumentAnnexeId(): ?int
    {
        return $this->documentAnnexeId;
    }

    public function getDocumentAnnexeTitre(): ?string
    {
        return $this->documentAnnexeTitre;
    }

    public function setDocumentAnnexeTitre(?string $documentAnnexeTitre): self
    {
        $this->documentAnnexeTitre = $documentAnnexeTitre;

        return $this;
    }

    public function getDocumentAnnexeDesc(): ?string
    {
        return $this->documentAnnexeDesc;
    }

    public function setDocumentAnnexeDesc(?string $documentAnnexeDesc): self
    {
        $this->documentAnnexeDesc = $documentAnnexeDesc;

        return $this;
    }

    public function getDocumentAnnexeAllOpp(): ?bool
    {
        return $this->documentAnnexeAllOpp;
    }

    public function setDocumentAnnexeAllOpp(?bool $documentAnnexeAllOpp): self
    {
        $this->documentAnnexeAllOpp = $documentAnnexeAllOpp;

        return $this;
    }

    public function getDocumentAnnexeFile(): ?FileManager
    {
        return $this->documentAnnexeFile;
    }

    public function setDocumentAnnexeFile(?FileManager $documentAnnexeFile): self
    {
        $this->documentAnnexeFile = $documentAnnexeFile;

        return $this;
    }

    public function getDocumentAnnexeOpp(): ?Opp
    {
        return $this->documentAnnexeOpp;
    }

    public function setDocumentAnnexeOpp(?Opp $documentAnnexeOpp): self
    {
        $this->documentAnnexeOpp = $documentAnnexeOpp;

        return $this;
    }


}
