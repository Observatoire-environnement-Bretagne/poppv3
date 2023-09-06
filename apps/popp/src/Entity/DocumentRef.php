<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DocumentRef
 *
 * @ORM\Table(name="document_ref", indexes={@ORM\Index(name="fki_document_ref_file_id", columns={"document_ref_file_id"}), @ORM\Index(name="fki_document_ref_langue_id", columns={"document_ref_langue_id"}), @ORM\Index(name="fki_document_ref_licence_id", columns={"document_ref_licence_id"})})
 * @ORM\Entity
 */
class DocumentRef
{
    /**
     * @var int
     *
     * @ORM\Column(name="document_ref_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="document_ref_document_ref_id_seq", allocationSize=1, initialValue=1)
     */
    private $documentRefId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_identifiant_int", type="string", length=255, nullable=true)
     */
    private $documentRefIdentifiantInt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_auteur", type="string", length=255, nullable=true)
     */
    private $documentRefAuteur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_desc", type="text", nullable=true)
     */
    private $documentRefDesc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="document_ref_date", type="datetime", nullable=true)
     */
    private $documentRefDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_commentaire_date", type="text", nullable=true)
     */
    private $documentRefCommentaireDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_type", type="string", length=255, nullable=true)
     */
    private $documentRefType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_format", type="string", length=255, nullable=true)
     */
    private $documentRefFormat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_source", type="string", length=255, nullable=true)
     */
    private $documentRefSource;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_site", type="string", length=255, nullable=true)
     */
    private $documentRefSite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_nom", type="string", length=255, nullable=true)
     */
    private $documentRefNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_sous_titre", type="string", length=255, nullable=true)
     */
    private $documentRefSousTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_heure", type="string", length=8, nullable=true)
     */
    private $documentRefHeure;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_periode", type="string", length=255, nullable=true)
     */
    private $documentRefPeriode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_moment", type="string", length=255, nullable=true)
     */
    private $documentRefMoment;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_lieu_conservation", type="string", length=255, nullable=true)
     */
    private $documentRefLieuConservation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_orientation", type="string", length=255, nullable=true)
     */
    private $documentRefOrientation;

    /**
     * @var float|null
     *
     * @ORM\Column(name="document_ref_altitude", type="float", precision=10, scale=0, nullable=true)
     */
    private $documentRefAltitude;

    /**
     * @var int|null
     *
     * @ORM\Column(name="document_ref_coef_maree", type="integer", nullable=true)
     */
    private $documentRefCoefMaree;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document_ref_cote_doc", type="string", length=255, nullable=true)
     */
    private $documentRefCoteDoc;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_ref_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $documentRefFile;

    /**
     * @var \Langue
     *
     * @ORM\ManyToOne(targetEntity="Langue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_ref_langue_id", referencedColumnName="langue_id")
     * })
     */
    private $documentRefLangue;

    /**
     * @var \Licence
     *
     * @ORM\ManyToOne(targetEntity="Licence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_ref_licence_id", referencedColumnName="licence_id")
     * })
     */
    private $documentRefLicence;

    public function getDocumentRefId(): ?int
    {
        return $this->documentRefId;
    }

    public function getDocumentRefIdentifiantInt(): ?string
    {
        return $this->documentRefIdentifiantInt;
    }

    public function setDocumentRefIdentifiantInt(?string $documentRefIdentifiantInt): self
    {
        $this->documentRefIdentifiantInt = $documentRefIdentifiantInt;

        return $this;
    }

    public function getDocumentRefAuteur(): ?string
    {
        return $this->documentRefAuteur;
    }

    public function setDocumentRefAuteur(?string $documentRefAuteur): self
    {
        $this->documentRefAuteur = $documentRefAuteur;

        return $this;
    }

    public function getDocumentRefDesc(): ?string
    {
        return $this->documentRefDesc;
    }

    public function setDocumentRefDesc(?string $documentRefDesc): self
    {
        $this->documentRefDesc = $documentRefDesc;

        return $this;
    }

    public function getDocumentRefDate(): ?\DateTimeInterface
    {
        return $this->documentRefDate;
    }

    public function setDocumentRefDate(?\DateTimeInterface $documentRefDate): self
    {
        $this->documentRefDate = $documentRefDate;

        return $this;
    }

    public function getDocumentRefCommentaireDate(): ?string
    {
        return $this->documentRefCommentaireDate;
    }

    public function setDocumentRefCommentaireDate(?string $documentRefCommentaireDate): self
    {
        $this->documentRefCommentaireDate = $documentRefCommentaireDate;

        return $this;
    }

    public function getDocumentRefType(): ?string
    {
        return $this->documentRefType;
    }

    public function setDocumentRefType(?string $documentRefType): self
    {
        $this->documentRefType = $documentRefType;

        return $this;
    }

    public function getDocumentRefFormat(): ?string
    {
        return $this->documentRefFormat;
    }

    public function setDocumentRefFormat(?string $documentRefFormat): self
    {
        $this->documentRefFormat = $documentRefFormat;

        return $this;
    }

    public function getDocumentRefSource(): ?string
    {
        return $this->documentRefSource;
    }

    public function setDocumentRefSource(?string $documentRefSource): self
    {
        $this->documentRefSource = $documentRefSource;

        return $this;
    }

    public function getDocumentRefSite(): ?string
    {
        return $this->documentRefSite;
    }

    public function setDocumentRefSite(?string $documentRefSite): self
    {
        $this->documentRefSite = $documentRefSite;

        return $this;
    }

    public function getDocumentRefNom(): ?string
    {
        return $this->documentRefNom;
    }

    public function setDocumentRefNom(?string $documentRefNom): self
    {
        $this->documentRefNom = $documentRefNom;

        return $this;
    }

    public function getDocumentRefSousTitre(): ?string
    {
        return $this->documentRefSousTitre;
    }

    public function setDocumentRefSousTitre(?string $documentRefSousTitre): self
    {
        $this->documentRefSousTitre = $documentRefSousTitre;

        return $this;
    }

    public function getDocumentRefHeure(): ?string
    {
        return $this->documentRefHeure;
    }

    public function setDocumentRefHeure(?string $documentRefHeure): self
    {
        $this->documentRefHeure = $documentRefHeure;

        return $this;
    }

    public function getDocumentRefPeriode(): ?string
    {
        return $this->documentRefPeriode;
    }

    public function setDocumentRefPeriode(?string $documentRefPeriode): self
    {
        $this->documentRefPeriode = $documentRefPeriode;

        return $this;
    }

    public function getDocumentRefMoment(): ?string
    {
        return $this->documentRefMoment;
    }

    public function setDocumentRefMoment(?string $documentRefMoment): self
    {
        $this->documentRefMoment = $documentRefMoment;

        return $this;
    }

    public function getDocumentRefLieuConservation(): ?string
    {
        return $this->documentRefLieuConservation;
    }

    public function setDocumentRefLieuConservation(?string $documentRefLieuConservation): self
    {
        $this->documentRefLieuConservation = $documentRefLieuConservation;

        return $this;
    }

    public function getDocumentRefOrientation(): ?string
    {
        return $this->documentRefOrientation;
    }

    public function setDocumentRefOrientation(?string $documentRefOrientation): self
    {
        $this->documentRefOrientation = $documentRefOrientation;

        return $this;
    }

    public function getDocumentRefAltitude(): ?float
    {
        return $this->documentRefAltitude;
    }

    public function setDocumentRefAltitude(?float $documentRefAltitude): self
    {
        $this->documentRefAltitude = $documentRefAltitude;

        return $this;
    }

    public function getDocumentRefCoefMaree(): ?int
    {
        return $this->documentRefCoefMaree;
    }

    public function setDocumentRefCoefMaree(?int $documentRefCoefMaree): self
    {
        $this->documentRefCoefMaree = $documentRefCoefMaree;

        return $this;
    }

    public function getDocumentRefCoteDoc(): ?string
    {
        return $this->documentRefCoteDoc;
    }

    public function setDocumentRefCoteDoc(?string $documentRefCoteDoc): self
    {
        $this->documentRefCoteDoc = $documentRefCoteDoc;

        return $this;
    }

    public function getDocumentRefFile(): ?FileManager
    {
        return $this->documentRefFile;
    }

    public function setDocumentRefFile(?FileManager $documentRefFile): self
    {
        $this->documentRefFile = $documentRefFile;

        return $this;
    }

    public function getDocumentRefLangue(): ?Langue
    {
        return $this->documentRefLangue;
    }

    public function setDocumentRefLangue(?Langue $documentRefLangue): self
    {
        $this->documentRefLangue = $documentRefLangue;

        return $this;
    }

    public function getDocumentRefLicence(): ?Licence
    {
        return $this->documentRefLicence;
    }

    public function setDocumentRefLicence(?Licence $documentRefLicence): self
    {
        $this->documentRefLicence = $documentRefLicence;

        return $this;
    }


}
