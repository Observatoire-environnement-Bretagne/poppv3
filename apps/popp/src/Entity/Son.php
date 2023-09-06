<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Son
 *
 * @ORM\Table(name="son", indexes={@ORM\Index(name="fki_son_struct_resp_id", columns={"son_struct_resp_id"}), @ORM\Index(name="fki_son_langue_id", columns={"son_langue_id"}), @ORM\Index(name="fki_son_file_id", columns={"son_file_id"}), @ORM\Index(name="fki_son_licence_id", columns={"son_licence_id"})})
 * @ORM\Entity
 */
class Son
{
    /**
     * @var int
     *
     * @ORM\Column(name="son_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="son_son_id_seq", allocationSize=1, initialValue=1)
     */
    private $sonId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_titre", type="string", length=255, nullable=true)
     */
    private $sonTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_auteur", type="string", length=255, nullable=true)
     */
    private $sonAuteur;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_presentation", type="text", nullable=true)
     */
    private $sonPresentation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_lien_paysage", type="text", nullable=true)
     */
    private $sonLienPaysage;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="son_date", type="datetime", nullable=true)
     */
    private $sonDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_type", type="string", length=255, nullable=true)
     */
    private $sonType;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_format", type="string", length=255, nullable=true)
     */
    private $sonFormat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_heure", type="string", length=8, nullable=true)
     */
    private $sonHeure;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_type_mat", type="string", length=255, nullable=true)
     */
    private $sonTypeMat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_traitement", type="string", length=255, nullable=true)
     */
    private $sonTraitement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_protocole", type="string", length=255, nullable=true)
     */
    private $sonProtocole;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_contexte", type="string", length=255, nullable=true)
     */
    private $sonContexte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_condition_meteo", type="string", length=255, nullable=true)
     */
    private $sonConditionMeteo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="son_num_photo", type="integer", nullable=true)
     */
    private $sonNumPhoto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="son_lieu", type="text", nullable=true)
     */
    private $sonLieu;

    /**
     * @var int|null
     *
     * @ORM\Column(name="son_duree", type="integer", nullable=true)
     */
    private $sonDuree;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="son_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $sonFile;

    /**
     * @var \Langue
     *
     * @ORM\ManyToOne(targetEntity="Langue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="son_langue_id", referencedColumnName="langue_id")
     * })
     */
    private $sonLangue;

    /**
     * @var \Licence
     *
     * @ORM\ManyToOne(targetEntity="Licence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="son_licence_id", referencedColumnName="licence_id")
     * })
     */
    private $sonLicence;

    /**
     * @var \PorteurOpp
     *
     * @ORM\ManyToOne(targetEntity="PorteurOpp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="son_struct_resp_id", referencedColumnName="porteur_opp_id")
     * })
     */
    private $sonStructResp;

    /**
     * @var \Serie
     *
     * @ORM\ManyToOne(targetEntity="Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="son_serie_id", referencedColumnName="serie_id")
     * })
     */
    private $sonSerie;

    public function getSonId(): ?int
    {
        return $this->sonId;
    }

    public function getSonTitre(): ?string
    {
        return $this->sonTitre;
    }

    public function setSonTitre(?string $sonTitre): self
    {
        $this->sonTitre = $sonTitre;

        return $this;
    }

    public function getSonAuteur(): ?string
    {
        return $this->sonAuteur;
    }

    public function setSonAuteur(?string $sonAuteur): self
    {
        $this->sonAuteur = $sonAuteur;

        return $this;
    }

    public function getSonPresentation(): ?string
    {
        return $this->sonPresentation;
    }

    public function setSonPresentation(?string $sonPresentation): self
    {
        $this->sonPresentation = $sonPresentation;

        return $this;
    }

    public function getSonLienPaysage(): ?string
    {
        return $this->sonLienPaysage;
    }

    public function setSonLienPaysage(?string $sonLienPaysage): self
    {
        $this->sonLienPaysage = $sonLienPaysage;

        return $this;
    }

    public function getSonDate(): ?\DateTimeInterface
    {
        return $this->sonDate;
    }

    public function setSonDate(?\DateTimeInterface $sonDate): self
    {
        $this->sonDate = $sonDate;

        return $this;
    }

    public function getSonType(): ?string
    {
        return $this->sonType;
    }

    public function setSonType(?string $sonType): self
    {
        $this->sonType = $sonType;

        return $this;
    }

    public function getSonFormat(): ?string
    {
        return $this->sonFormat;
    }

    public function setSonFormat(?string $sonFormat): self
    {
        $this->sonFormat = $sonFormat;

        return $this;
    }

    public function getSonHeure(): ?string
    {
        return $this->sonHeure;
    }

    public function setSonHeure(?string $sonHeure): self
    {
        $this->sonHeure = $sonHeure;

        return $this;
    }

    public function getSonTypeMat(): ?string
    {
        return $this->sonTypeMat;
    }

    public function setSonTypeMat(?string $sonTypeMat): self
    {
        $this->sonTypeMat = $sonTypeMat;

        return $this;
    }

    public function getSonTraitement(): ?string
    {
        return $this->sonTraitement;
    }

    public function setSonTraitement(?string $sonTraitement): self
    {
        $this->sonTraitement = $sonTraitement;

        return $this;
    }

    public function getSonProtocole(): ?string
    {
        return $this->sonProtocole;
    }

    public function setSonProtocole(?string $sonProtocole): self
    {
        $this->sonProtocole = $sonProtocole;

        return $this;
    }

    public function getSonContexte(): ?string
    {
        return $this->sonContexte;
    }

    public function setSonContexte(?string $sonContexte): self
    {
        $this->sonContexte = $sonContexte;

        return $this;
    }

    public function getSonConditionMeteo(): ?string
    {
        return $this->sonConditionMeteo;
    }

    public function setSonConditionMeteo(?string $sonConditionMeteo): self
    {
        $this->sonConditionMeteo = $sonConditionMeteo;

        return $this;
    }

    public function getSonNumPhoto(): ?int
    {
        return $this->sonNumPhoto;
    }

    public function setSonNumPhoto(?int $sonNumPhoto): self
    {
        $this->sonNumPhoto = $sonNumPhoto;

        return $this;
    }

    public function getSonLieu(): ?string
    {
        return $this->sonLieu;
    }

    public function setSonLieu(?string $sonLieu): self
    {
        $this->sonLieu = $sonLieu;

        return $this;
    }

    public function getSonDuree(): ?int
    {
        return $this->sonDuree;
    }

    public function setSonDuree(?int $sonDuree): self
    {
        $this->sonDuree = $sonDuree;

        return $this;
    }

    public function getSonFile(): ?FileManager
    {
        return $this->sonFile;
    }

    public function setSonFile(?FileManager $sonFile): self
    {
        $this->sonFile = $sonFile;

        return $this;
    }

    public function getSonLangue(): ?Langue
    {
        return $this->sonLangue;
    }

    public function setSonLangue(?Langue $sonLangue): self
    {
        $this->sonLangue = $sonLangue;

        return $this;
    }

    public function getSonLicence(): ?Licence
    {
        return $this->sonLicence;
    }

    public function setSonLicence(?Licence $sonLicence): self
    {
        $this->sonLicence = $sonLicence;

        return $this;
    }

    public function getSonStructResp(): ?PorteurOpp
    {
        return $this->sonStructResp;
    }

    public function setSonStructResp(?PorteurOpp $sonStructResp): self
    {
        $this->sonStructResp = $sonStructResp;

        return $this;
    }

    public function getSonSerie(): ?Serie
    {
        return $this->sonSerie;
    }

    public function setSonSerie(?Serie $sonSerie): self
    {
        $this->sonSerie = $sonSerie;

        return $this;
    }


}
