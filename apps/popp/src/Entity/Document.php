<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="document", indexes={@ORM\Index(name="fki_document_file_id", columns={"document_file_id"})})
 * @ORM\Entity
 */
class Document
{
    /**
     * @var int
     *
     * @ORM\Column(name="document_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="document_document_id_seq", allocationSize=1, initialValue=1)
     */
    private $documentId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_titre", type="string", length=255, nullable=true)
     */
    private $documentTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_legende", type="text", nullable=true)
     */
    private $documentLegende;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $documentFile;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $documentSerie;

    public function getDocumentId(): ?int
    {
        return $this->documentId;
    }

    public function getDocumentTitre(): ?string
    {
        return $this->documentTitre;
    }

    public function setDocumentTitre(?string $documentTitre): self
    {
        $this->documentTitre = $documentTitre;

        return $this;
    }

    public function getDocumentLegende(): ?string
    {
        return $this->documentLegende;
    }

    public function setDocumentLegende(?string $documentLegende): self
    {
        $this->documentLegende = $documentLegende;

        return $this;
    }

    public function getDocumentFile(): ?FileManager
    {
        return $this->documentFile;
    }

    public function setDocumentFile(?FileManager $documentFile): self
    {
        $this->documentFile = $documentFile;

        return $this;
    }

    public function getDocumentSerie(): ?Serie
    {
        return $this->documentSerie;
    }

    public function setDocumentSerie(?Serie $documentSerie): self
    {
        $this->documentSerie = $documentSerie;

        return $this;
    }


}
