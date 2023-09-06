<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhotoRepository;

/**
 * Photo
 *
 * @ORM\Table(name="photo", indexes={@ORM\Index(name="fki_photo_format_id", columns={"photo_format_id"}), @ORM\Index(name="fki_photo_licence_id", columns={"photo_licence_id"}), @ORM\Index(name="fki_photo_file_id", columns={"photo_file_id"}), @ORM\Index(name="fki_serie_file_id", columns={"photo_serie_id"})})
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * @var int
     *
     * @ORM\Column(name="photo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="photo_photo_id_seq", allocationSize=1, initialValue=1)
     */
    private $photoId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_titre", type="string", length=255, nullable=true)
     */
    private $photoTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_auteur", type="string", length=255, nullable=true)
     */
    private $photoAuteur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_desc_changement", type="text", nullable=true)
     */
    private $photoDescChangement;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="photo_date_desc", type="datetime", nullable=true)
     */
    private $photoDateDesc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="photo_date_prise", type="datetime", nullable=true)
     */
    private $photoDatePrise;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_identifiant_int", type="string", length=255, nullable=true)
     */
    private $photoIdentifiantInt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_heure", type="string", length=8, nullable=true)
     */
    private $photoHeure;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_type_appareil", type="string", length=255, nullable=true)
     */
    private $photoTypeAppareil;

    /**
     * @var float|null
     *
     * @ORM\Column(name="photo_focale", type="float", precision=10, scale=0, nullable=true)
     */
    private $photoFocale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_ouverture_dia", type="string", length=255, nullable=true)
     */
    private $photoOuvertureDia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_type_film", type="string", length=255, nullable=true)
     */
    private $photoTypeFilm;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_iso", type="string", length=255, nullable=true)
     */
    private $photoIso;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_poids_origine", type="string", length=255, nullable=true)
     */
    private $photoPoidsOrigine;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_inclinaison", type="string", length=255, nullable=true)
     */
    private $photoInclinaison;

    /**
     * @var float|null
     *
     * @ORM\Column(name="photo_hauteur", type="float", precision=10, scale=0, nullable=true)
     */
    private $photoHauteur;

    /**
     * @var float|null
     *
     * @ORM\Column(name="photo_orientation", type="float", precision=10, scale=0, nullable=true)
     */
    private $photoOrientation;

    /**
     * @var int|null
     *
     * @ORM\Column(name="photo_altitude", type="integer", nullable=true)
     */
    private $photoAltitude;

    /**
     * @var int|null
     *
     * @ORM\Column(name="photo_coef_maree", type="integer", nullable=true)
     */
    private $photoCoefMaree;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="photo_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $photoFile;

    /**
     * @var \Format
     *
     * @ORM\ManyToOne(targetEntity="Format")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="photo_format_id", referencedColumnName="format_id")
     * })
     */
    private $photoFormat;

    /**
     * @var \Licence
     *
     * @ORM\ManyToOne(targetEntity="Licence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="photo_licence_id", referencedColumnName="licence_id")
     * })
     */
    private $photoLicence;

    /**
     * @var \Licence
     *
     * @ORM\ManyToOne(targetEntity="Licence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="photo_licence_fiche_id", referencedColumnName="licence_id")
     * })
     */
    private $photoLicenceFiche;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="photo_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $photoSerie;

    public function getPhotoId(): ?int
    {
        return $this->photoId;
    }

    public function getPhotoTitre(): ?string
    {
        return $this->photoTitre;
    }

    public function setPhotoTitre(?string $photoTitre): self
    {
        $this->photoTitre = $photoTitre;

        return $this;
    }

    public function getPhotoAuteur(): ?string
    {
        return $this->photoAuteur;
    }

    public function setPhotoAuteur(?string $photoAuteur): self
    {
        $this->photoAuteur = $photoAuteur;

        return $this;
    }

    public function getPhotoDescChangement(): ?string
    {
        return $this->photoDescChangement;
    }

    public function setPhotoDescChangement(?string $photoDescChangement): self
    {
        $this->photoDescChangement = $photoDescChangement;

        return $this;
    }

    public function getPhotoDateDesc(): ?\DateTimeInterface
    {
        return $this->photoDateDesc;
    }

    public function setPhotoDateDesc(?\DateTimeInterface $photoDateDesc): self
    {
        $this->photoDateDesc = $photoDateDesc;

        return $this;
    }

    public function getPhotoDatePrise(): ?\DateTimeInterface
    {
        return $this->photoDatePrise;
    }

    public function setPhotoDatePrise(?\DateTimeInterface $photoDatePrise): self
    {
        $this->photoDatePrise = $photoDatePrise;

        return $this;
    }

    public function getPhotoIdentifiantInt(): ?string
    {
        return $this->photoIdentifiantInt;
    }

    public function setPhotoIdentifiantInt(?string $photoIdentifiantInt): self
    {
        $this->photoIdentifiantInt = $photoIdentifiantInt;

        return $this;
    }

    public function getPhotoHeure(): ?string
    {
        return $this->photoHeure;
    }

    public function setPhotoHeure(?string $photoHeure): self
    {
        $this->photoHeure = $photoHeure;

        return $this;
    }

    public function getPhotoTypeAppareil(): ?string
    {
        return $this->photoTypeAppareil;
    }

    public function setPhotoTypeAppareil(?string $photoTypeAppareil): self
    {
        $this->photoTypeAppareil = $photoTypeAppareil;

        return $this;
    }

    public function getPhotoFocale(): ?float
    {
        return $this->photoFocale;
    }

    public function setPhotoFocale(?float $photoFocale): self
    {
        $this->photoFocale = $photoFocale;

        return $this;
    }

    public function getPhotoOuvertureDia(): ?string
    {
        return $this->photoOuvertureDia;
    }

    public function setPhotoOuvertureDia(?string $photoOuvertureDia): self
    {
        $this->photoOuvertureDia = $photoOuvertureDia;

        return $this;
    }

    public function getPhotoTypeFilm(): ?string
    {
        return $this->photoTypeFilm;
    }

    public function setPhotoTypeFilm(?string $photoTypeFilm): self
    {
        $this->photoTypeFilm = $photoTypeFilm;

        return $this;
    }

    public function getPhotoIso(): ?string
    {
        return $this->photoIso;
    }

    public function setPhotoIso(?string $photoIso): self
    {
        $this->photoIso = $photoIso;

        return $this;
    }

    public function getPhotoPoidsOrigine(): ?string
    {
        return $this->photoPoidsOrigine;
    }

    public function setPhotoPoidsOrigine(?string $photoPoidsOrigine): self
    {
        $this->photoPoidsOrigine = $photoPoidsOrigine;

        return $this;
    }

    public function getPhotoInclinaison(): ?string
    {
        return $this->photoInclinaison;
    }

    public function setPhotoInclinaison(?string $photoInclinaison): self
    {
        $this->photoInclinaison = $photoInclinaison;

        return $this;
    }

    public function getPhotoHauteur(): ?float
    {
        return $this->photoHauteur;
    }

    public function setPhotoHauteur(?float $photoHauteur): self
    {
        $this->photoHauteur = $photoHauteur;

        return $this;
    }

    public function getPhotoOrientation(): ?float
    {
        return $this->photoOrientation;
    }

    public function setPhotoOrientation(?float $photoOrientation): self
    {
        $this->photoOrientation = $photoOrientation;

        return $this;
    }

    public function getPhotoAltitude(): ?int
    {
        return $this->photoAltitude;
    }

    public function setPhotoAltitude(?int $photoAltitude): self
    {
        $this->photoAltitude = $photoAltitude;

        return $this;
    }

    public function getPhotoCoefMaree(): ?int
    {
        return $this->photoCoefMaree;
    }

    public function setPhotoCoefMaree(?int $photoCoefMaree): self
    {
        $this->photoCoefMaree = $photoCoefMaree;

        return $this;
    }

    public function getPhotoFile(): ?FileManager
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?FileManager $photoFile): self
    {
        $this->photoFile = $photoFile;

        return $this;
    }

    public function getPhotoFormat(): ?Format
    {
        return $this->photoFormat;
    }

    public function setPhotoFormat(?Format $photoFormat): self
    {
        $this->photoFormat = $photoFormat;

        return $this;
    }

    public function getPhotoLicence(): ?Licence
    {
        return $this->photoLicence;
    }

    public function setPhotoLicence(?Licence $photoLicence): self
    {
        $this->photoLicence = $photoLicence;

        return $this;
    }

    public function getPhotoLicenceFiche(): ?Licence
    {
        return $this->photoLicenceFiche;
    }

    public function setPhotoLicenceFiche(?Licence $photoLicenceFiche): self
    {
        $this->photoLicenceFiche = $photoLicenceFiche;

        return $this;
    }

    public function getPhotoSerie(): ?Serie
    {
        return $this->photoSerie;
    }

    public function setPhotoSerie(?Serie $photoSerie): self
    {
        $this->photoSerie = $photoSerie;

        return $this;
    }


}
