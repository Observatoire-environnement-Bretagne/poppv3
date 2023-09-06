<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PorteurOpp
 *
 * @ORM\Table(name="porteur_opp", indexes={@ORM\Index(name="fki_porteur_opp_logo_id", columns={"porteur_opp_logo_id"})})
 * @ORM\Entity
 */
class PorteurOpp
{
    /**
     * @var int
     *
     * @ORM\Column(name="porteur_opp_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="porteur_opp_porteur_opp_id_seq", allocationSize=1, initialValue=1)
     */
    private $porteurOppId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_nom", type="string", length=255, nullable=true)
     */
    private $porteurOppNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_desc_courte", type="text", nullable=true)
     */
    private $porteurOppDescCourte;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_adresse", type="text", nullable=true)
     */
    private $porteurOppAdresse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_contact_ref", type="text", nullable=true)
     */
    private $porteurOppContactRef;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_desc_tech", type="text", nullable=true)
     */
    private $porteurOppDescTech;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_email", type="string", length=255, nullable=true)
     */
    private $porteurOppEmail;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_telephone", type="string", length=255, nullable=true)
     */
    private $porteurOppTelephone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_site_web", type="string", length=255, nullable=true)
     */
    private $porteurOppSiteWeb;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porteur_opp_preocupation_paysagere", type="text", nullable=true)
     */
    private $porteurOppPreocupationPaysagere;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="porteur_opp_financeur ", type="boolean", nullable=true)
     */
    private $porteurOppFinanceur  = false;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="porteur_opp_logo_id", referencedColumnName="file_manager_id")
     * })
     */
    private $porteurOppLogo;

    public function getPorteurOppId(): ?int
    {
        return $this->porteurOppId;
    }

    public function getPorteurOppNom(): ?string
    {
        return $this->porteurOppNom;
    }

    public function setPorteurOppNom(?string $porteurOppNom): self
    {
        $this->porteurOppNom = $porteurOppNom;

        return $this;
    }

    public function getPorteurOppDescCourte(): ?string
    {
        return $this->porteurOppDescCourte;
    }

    public function setPorteurOppDescCourte(?string $porteurOppDescCourte): self
    {
        $this->porteurOppDescCourte = $porteurOppDescCourte;

        return $this;
    }

    public function getPorteurOppAdresse(): ?string
    {
        return $this->porteurOppAdresse;
    }

    public function setPorteurOppAdresse(?string $porteurOppAdresse): self
    {
        $this->porteurOppAdresse = $porteurOppAdresse;

        return $this;
    }

    public function getPorteurOppContactRef(): ?string
    {
        return $this->porteurOppContactRef;
    }

    public function setPorteurOppContactRef(?string $porteurOppContactRef): self
    {
        $this->porteurOppContactRef = $porteurOppContactRef;

        return $this;
    }

    public function getPorteurOppDescTech(): ?string
    {
        return $this->porteurOppDescTech;
    }

    public function setPorteurOppDescTech(?string $porteurOppDescTech): self
    {
        $this->porteurOppDescTech = $porteurOppDescTech;

        return $this;
    }

    public function getPorteurOppEmail(): ?string
    {
        return $this->porteurOppEmail;
    }

    public function setPorteurOppEmail(?string $porteurOppEmail): self
    {
        $this->porteurOppEmail = $porteurOppEmail;

        return $this;
    }

    public function getPorteurOppTelephone(): ?string
    {
        return $this->porteurOppTelephone;
    }

    public function setPorteurOppTelephone(?string $porteurOppTelephone): self
    {
        $this->porteurOppTelephone = $porteurOppTelephone;

        return $this;
    }

    public function getPorteurOppSiteWeb(): ?string
    {
        return $this->porteurOppSiteWeb;
    }

    public function setPorteurOppSiteWeb(?string $porteurOppSiteWeb): self
    {
        $this->porteurOppSiteWeb = $porteurOppSiteWeb;

        return $this;
    }

    public function getPorteurOppPreocupationPaysagere(): ?string
    {
        return $this->porteurOppPreocupationPaysagere;
    }

    public function setPorteurOppPreocupationPaysagere(?string $porteurOppPreocupationPaysagere): self
    {
        $this->porteurOppPreocupationPaysagere = $porteurOppPreocupationPaysagere;

        return $this;
    }

    public function getPorteurOppFinanceur(): ?bool
    {
        return $this->porteurOppFinanceur;
    }

    public function setPorteurOppFinanceur(?bool $porteurOppFinanceur): self
    {
        $this->porteurOppFinanceur = $porteurOppFinanceur;

        return $this;
    }

    public function getPorteurOppLogo(): ?FileManager
    {
        return $this->porteurOppLogo;
    }

    public function setPorteurOppLogo(?FileManager $porteurOppLogo): self
    {
        $this->porteurOppLogo = $porteurOppLogo;

        return $this;
    }


}
