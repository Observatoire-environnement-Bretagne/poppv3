<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SerieRepository;

/**
 * Serie
 *
 * @ORM\Table(name="serie", indexes={@ORM\Index(name="fki_serie_user_crea", columns={"serie_user_crea"}), @ORM\Index(name="fki_serie_refdoc_id", columns={"serie_refdoc_id"}), @ORM\Index(name="fki_serie_photo_ign_id", columns={"serie_photo_ign_id"}), @ORM\Index(name="fki_serie_porteur_opp_id", columns={"serie_porteur_opp_id"}), @ORM\Index(name="fki_serie_region_id", columns={"serie_region_id"}), @ORM\Index(name="fki_serie_photo_trepied_id", columns={"serie_photo_trepied_id"}), @ORM\Index(name="fki_serie_photo_context_id", columns={"serie_photo_context_id"}), @ORM\Index(name="fki_serie_photo_aerienne_id", columns={"serie_photo_aerienne_id"}), @ORM\Index(name="fki_serie_commune_id", columns={"serie_commune_id"}), @ORM\Index(name="fki_serie_pays_id", columns={"serie_pays_id"}), @ORM\Index(name="fki_serie_user_maj", columns={"serie_user_maj"}), @ORM\Index(name="fki_serie_langue_id", columns={"serie_langue_id"}), @ORM\Index(name="fk_serie_typologie_id", columns={"serie_typologie_id"}), @ORM\Index(name="fki_serie_unite_paysagere_id", columns={"serie_unite_paysagere_id"}), @ORM\Index(name="fki_serie_opp_id", columns={"serie_opp_id"}), @ORM\Index(name="fki_serie_croquis_id", columns={"serie_croquis_id"}), @ORM\Index(name="fki_serie_departement_id", columns={"serie_departement_id"}), @ORM\Index(name="fki_serie_ensemble_paysage_id", columns={"serie_ensemble_paysage_id"}), @ORM\Index(name="fki_serie_format_id", columns={"serie_format_id"})})
 * @ORM\Entity(repositoryClass=SerieRepository::class)
 */
class Serie
{
    /**
     * @var int
     *
     * @ORM\Column(name="serie_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="serie_serie_id_seq", allocationSize=1, initialValue=1)
     */
    private $serieId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_titre", type="string", length=255, nullable=true)
     */
    private $serieTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_intention", type="text", nullable=true)
     */
    private $serieIntention;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_desc", type="text", nullable=true)
     */
    private $serieDesc;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="serie_date", type="datetime", nullable=true)
     */
    private $serieDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_identifiant_serie", type="string", length=255, nullable=true)
     */
    private $serieIdentifiantSerie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_identifiant_int", type="string", length=255, nullable=true)
     */
    private $serieIdentifiantInt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_adresse", type="string", length=255, nullable=true)
     */
    private $serieAdresse;

    /**
     * @var int|null
     *
     * @ORM\Column(name="serie_freq_interval", type="integer", nullable=true)
     */
    private $serieFreqInterval;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_freq_period", type="string", length=255, nullable=true)
     */
    private $serieFreqPeriod;

    /**
     * @var float|null
     *
     * @ORM\Column(name="serie_geom_x", type="float", precision=10, scale=0, nullable=true)
     */
    private $serieGeomX;

    /**
     * @var float|null
     *
     * @ORM\Column(name="serie_geom_y", type="float", precision=10, scale=0, nullable=true)
     */
    private $serieGeomY;

    /**
     * @var string|null
     *
     * @ORM\Column(name="serie_obs_rephoto", type="text", nullable=true)
     */
    private $serieObsRephoto;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="serie_publie", type="boolean", nullable=true, options={"default"="1"})
     */
    private $seriePublie = true;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="serie_date_crea", type="datetime", nullable=true)
     */
    private $serieDateCrea;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="serie_date_maj", type="datetime", nullable=true)
     */
    private $serieDateMaj;

    /**
     * @var \Commune
     *
     * @ORM\ManyToOne(targetEntity="Commune")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_commune_id", referencedColumnName="commune_id")
     * })
     */
    private $serieCommune;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_croquis_id", referencedColumnName="file_manager_id")
     * })
     */
    private $serieCroquis;

    /**
     * @var \Departement
     *
     * @ORM\ManyToOne(targetEntity="Departement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_departement_id", referencedColumnName="departement_id")
     * })
     */
    private $serieDepartement;

    /**
     * @var \EnsemblePaysager
     *
     * @ORM\ManyToOne(targetEntity="EnsemblePaysager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_ensemble_paysage_id", referencedColumnName="ensemble_paysager_id")
     * })
     */
    private $serieEnsemblePaysage;

    /**
     * @var \Format
     *
     * @ORM\ManyToOne(targetEntity="Format")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_format_id", referencedColumnName="format_id")
     * })
     */
    private $serieFormat;

    /**
     * @var \Langue
     *
     * @ORM\ManyToOne(targetEntity="Langue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_langue_id", referencedColumnName="langue_id")
     * })
     */
    private $serieLangue;

    /**
     * @var \Opp
     *
     * @ORM\ManyToOne(targetEntity="Opp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_opp_id", referencedColumnName="opp_id")
     * })
     */
    private $serieOpp;

    /**
     * @var \Pays
     *
     * @ORM\ManyToOne(targetEntity="Pays")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_pays_id", referencedColumnName="pays_id")
     * })
     */
    private $seriePays;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_photo_aerienne_id", referencedColumnName="file_manager_id")
     * })
     */
    private $seriePhotoAerienne;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_photo_context_id", referencedColumnName="file_manager_id")
     * })
     */
    private $seriePhotoContext;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_photo_ign_id", referencedColumnName="file_manager_id")
     * })
     */
    private $seriePhotoIgn;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_photo_trepied_id", referencedColumnName="file_manager_id")
     * })
     */
    private $seriePhotoTrepied;

    /**
     * @var \PorteurOpp
     *
     * @ORM\ManyToOne(targetEntity="PorteurOpp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_porteur_opp_id", referencedColumnName="porteur_opp_id")
     * })
     */
    private $seriePorteurOpp;

    /**
     * @var \DocumentRef
     *
     * @ORM\ManyToOne(targetEntity="DocumentRef")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_refdoc_id", referencedColumnName="document_ref_id")
     * })
     */
    private $serieRefdoc;

    /**
     * @var \Region
     *
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_region_id", referencedColumnName="region_id")
     * })
     */
    private $serieRegion;

    /**
     * @var \TypologiePaysage
     *
     * @ORM\ManyToOne(targetEntity="TypologiePaysage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_typologie_id", referencedColumnName="typologie_paysage_id")
     * })
     */
    private $serieTypologie;

    /**
     * @var \UnitePaysage
     *
     * @ORM\ManyToOne(targetEntity="UnitePaysage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_unite_paysagere_id", referencedColumnName="unite_paysage_id")
     * })
     */
    private $serieUnitePaysagere;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_user_crea", referencedColumnName="id")
     * })
     */
    private $serieUserCrea;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serie_user_maj", referencedColumnName="id")
     * })
     */
    private $serieUserMaj;

    public function getSerieId(): ?int
    {
        return $this->serieId;
    }

    public function getSerieTitre(): ?string
    {
        return $this->serieTitre;
    }

    public function setSerieTitre(?string $serieTitre): self
    {
        $this->serieTitre = $serieTitre;

        return $this;
    }

    public function getSerieIntention(): ?string
    {
        return $this->serieIntention;
    }

    public function setSerieIntention(?string $serieIntention): self
    {
        $this->serieIntention = $serieIntention;

        return $this;
    }

    public function getSerieDesc(): ?string
    {
        return $this->serieDesc;
    }

    public function setSerieDesc(?string $serieDesc): self
    {
        $this->serieDesc = $serieDesc;

        return $this;
    }

    public function getSerieDate(): ?\DateTimeInterface
    {
        return $this->serieDate;
    }

    public function setSerieDate(?\DateTimeInterface $serieDate): self
    {
        $this->serieDate = $serieDate;

        return $this;
    }

    public function getSerieIdentifiantSerie(): ?string
    {
        return $this->serieIdentifiantSerie;
    }

    public function setSerieIdentifiantSerie(?string $serieIdentifiantSerie): self
    {
        $this->serieIdentifiantSerie = $serieIdentifiantSerie;

        return $this;
    }

    public function getSerieIdentifiantInt(): ?string
    {
        return $this->serieIdentifiantInt;
    }

    public function setSerieIdentifiantInt(?string $serieIdentifiantInt): self
    {
        $this->serieIdentifiantInt = $serieIdentifiantInt;

        return $this;
    }

    public function getSerieAdresse(): ?string
    {
        return $this->serieAdresse;
    }

    public function setSerieAdresse(?string $serieAdresse): self
    {
        $this->serieAdresse = $serieAdresse;

        return $this;
    }

    public function getSerieFreqInterval(): ?int
    {
        return $this->serieFreqInterval;
    }

    public function setSerieFreqInterval(?int $serieFreqInterval): self
    {
        $this->serieFreqInterval = $serieFreqInterval;

        return $this;
    }

    public function getSerieFreqPeriod(): ?string
    {
        return $this->serieFreqPeriod;
    }

    public function setSerieFreqPeriod(?string $serieFreqPeriod): self
    {
        $this->serieFreqPeriod = $serieFreqPeriod;

        return $this;
    }

    public function getSerieGeomX(): ?float
    {
        return $this->serieGeomX;
    }

    public function setSerieGeomX(?float $serieGeomX): self
    {
        $this->serieGeomX = $serieGeomX;

        return $this;
    }

    public function getSerieGeomY(): ?float
    {
        return $this->serieGeomY;
    }

    public function setSerieGeomY(?float $serieGeomY): self
    {
        $this->serieGeomY = $serieGeomY;

        return $this;
    }

    public function getSerieObsRephoto(): ?string
    {
        return $this->serieObsRephoto;
    }

    public function setSerieObsRephoto(?string $serieObsRephoto): self
    {
        $this->serieObsRephoto = $serieObsRephoto;

        return $this;
    }

    public function getSeriePublie(): ?bool
    {
        return $this->seriePublie;
    }

    public function setSeriePublie(?bool $seriePublie): self
    {
        $this->seriePublie = $seriePublie;

        return $this;
    }

    public function getSerieDateCrea(): ?\DateTimeInterface
    {
        return $this->serieDateCrea;
    }

    public function setSerieDateCrea(?\DateTimeInterface $serieDateCrea): self
    {
        $this->serieDateCrea = $serieDateCrea;

        return $this;
    }

    public function getSerieDateMaj(): ?\DateTimeInterface
    {
        return $this->serieDateMaj;
    }

    public function setSerieDateMaj(?\DateTimeInterface $serieDateMaj): self
    {
        $this->serieDateMaj = $serieDateMaj;

        return $this;
    }

    public function getSerieCommune(): ?Commune
    {
        return $this->serieCommune;
    }

    public function setSerieCommune(?Commune $serieCommune): self
    {
        $this->serieCommune = $serieCommune;

        return $this;
    }

    public function getSerieCroquis(): ?FileManager
    {
        return $this->serieCroquis;
    }

    public function setSerieCroquis(?FileManager $serieCroquis): self
    {
        $this->serieCroquis = $serieCroquis;

        return $this;
    }

    public function getSerieDepartement(): ?Departement
    {
        return $this->serieDepartement;
    }

    public function setSerieDepartement(?Departement $serieDepartement): self
    {
        $this->serieDepartement = $serieDepartement;

        return $this;
    }

    public function getSerieEnsemblePaysage(): ?EnsemblePaysager
    {
        return $this->serieEnsemblePaysage;
    }

    public function setSerieEnsemblePaysage(?EnsemblePaysager $serieEnsemblePaysage): self
    {
        $this->serieEnsemblePaysage = $serieEnsemblePaysage;

        return $this;
    }

    public function getSerieFormat(): ?Format
    {
        return $this->serieFormat;
    }

    public function setSerieFormat(?Format $serieFormat): self
    {
        $this->serieFormat = $serieFormat;

        return $this;
    }

    public function getSerieLangue(): ?Langue
    {
        return $this->serieLangue;
    }

    public function setSerieLangue(?Langue $serieLangue): self
    {
        $this->serieLangue = $serieLangue;

        return $this;
    }

    public function getSerieOpp(): ?Opp
    {
        return $this->serieOpp;
    }

    public function setSerieOpp(?Opp $serieOpp): self
    {
        $this->serieOpp = $serieOpp;

        return $this;
    }

    public function getSeriePays(): ?Pays
    {
        return $this->seriePays;
    }

    public function setSeriePays(?Pays $seriePays): self
    {
        $this->seriePays = $seriePays;

        return $this;
    }

    public function getSeriePhotoAerienne(): ?FileManager
    {
        return $this->seriePhotoAerienne;
    }

    public function setSeriePhotoAerienne(?FileManager $seriePhotoAerienne): self
    {
        $this->seriePhotoAerienne = $seriePhotoAerienne;

        return $this;
    }

    public function getSeriePhotoContext(): ?FileManager
    {
        return $this->seriePhotoContext;
    }

    public function setSeriePhotoContext(?FileManager $seriePhotoContext): self
    {
        $this->seriePhotoContext = $seriePhotoContext;

        return $this;
    }

    public function getSeriePhotoIgn(): ?FileManager
    {
        return $this->seriePhotoIgn;
    }

    public function setSeriePhotoIgn(?FileManager $seriePhotoIgn): self
    {
        $this->seriePhotoIgn = $seriePhotoIgn;

        return $this;
    }

    public function getSeriePhotoTrepied(): ?FileManager
    {
        return $this->seriePhotoTrepied;
    }

    public function setSeriePhotoTrepied(?FileManager $seriePhotoTrepied): self
    {
        $this->seriePhotoTrepied = $seriePhotoTrepied;

        return $this;
    }

    public function getSeriePorteurOpp(): ?PorteurOpp
    {
        return $this->seriePorteurOpp;
    }

    public function setSeriePorteurOpp(?PorteurOpp $seriePorteurOpp): self
    {
        $this->seriePorteurOpp = $seriePorteurOpp;

        return $this;
    }

    public function getSerieRefdoc(): ?DocumentRef
    {
        return $this->serieRefdoc;
    }

    public function setSerieRefdoc(?DocumentRef $serieRefdoc): self
    {
        $this->serieRefdoc = $serieRefdoc;

        return $this;
    }

    public function getSerieRegion(): ?Region
    {
        return $this->serieRegion;
    }

    public function setSerieRegion(?Region $serieRegion): self
    {
        $this->serieRegion = $serieRegion;

        return $this;
    }

    public function getSerieTypologie(): ?TypologiePaysage
    {
        return $this->serieTypologie;
    }

    public function setSerieTypologie(?TypologiePaysage $serieTypologie): self
    {
        $this->serieTypologie = $serieTypologie;

        return $this;
    }

    public function getSerieUnitePaysagere(): ?UnitePaysage
    {
        return $this->serieUnitePaysagere;
    }

    public function setSerieUnitePaysagere(?UnitePaysage $serieUnitePaysagere): self
    {
        $this->serieUnitePaysagere = $serieUnitePaysagere;

        return $this;
    }

    public function getSerieUserCrea(): ?Users
    {
        return $this->serieUserCrea;
    }

    public function setSerieUserCrea(?Users $serieUserCrea): self
    {
        $this->serieUserCrea = $serieUserCrea;

        return $this;
    }

    public function getSerieUserMaj(): ?Users
    {
        return $this->serieUserMaj;
    }

    public function setSerieUserMaj(?Users $serieUserMaj): self
    {
        $this->serieUserMaj = $serieUserMaj;

        return $this;
    }


}
