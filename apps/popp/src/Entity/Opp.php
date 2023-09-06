<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Opp
 *
 * @ORM\Table(name="opp")
 * @ORM\Entity
 */
class Opp
{
    /**
     * @var int
     *
     * @ORM\Column(name="opp_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="opp_opp_id_seq", allocationSize=1, initialValue=1)
     */
    private $oppId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opp_nom", type="string", length=255, nullable=true)
     */
    private $oppNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opp_desc", type="text", nullable=true)
     */
    private $oppDesc;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opp_technicite", type="text", nullable=true)
     */
    private $oppTechnicite;

    /**
     * @var int|null
     *
     * @ORM\Column(name="opp_annee_creation", type="integer", nullable=true)
     */
    private $oppAnneeCreation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opp_niv_territ", type="string", length=255, nullable=true)
     */
    private $oppNivTerrit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opp_valo", type="string", length=255, nullable=true)
     */
    private $oppValo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="opp_poids", type="integer", nullable=true)
     */
    private $oppPoids;

    /**
     * @var \PorteurOpp
     *
     * @ORM\ManyToOne(targetEntity="PorteurOpp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opp_porteur_opp_id", referencedColumnName="porteur_opp_id")
     * })
     */
    private $oppPorteurOpp;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="opp_participative", type="boolean", nullable=true)
     */
    private $oppParticipative;

    public function getOppId(): ?int
    {
        return $this->oppId;
    }

    public function getOppNom(): ?string
    {
        return $this->oppNom;
    }

    public function setOppNom(?string $oppNom): self
    {
        $this->oppNom = $oppNom;

        return $this;
    }

    public function getOppDesc(): ?string
    {
        return $this->oppDesc;
    }

    public function setOppDesc(?string $oppDesc): self
    {
        $this->oppDesc = $oppDesc;

        return $this;
    }

    public function getOppTechnicite(): ?string
    {
        return $this->oppTechnicite;
    }

    public function setOppTechnicite(?string $oppTechnicite): self
    {
        $this->oppTechnicite = $oppTechnicite;

        return $this;
    }

    public function getOppAnneeCreation(): ?int
    {
        return $this->oppAnneeCreation;
    }

    public function setOppAnneeCreation(?int $oppAnneeCreation): self
    {
        $this->oppAnneeCreation = $oppAnneeCreation;

        return $this;
    }

    public function getOppNivTerrit(): ?string
    {
        return $this->oppNivTerrit;
    }

    public function setOppNivTerrit(?string $oppNivTerrit): self
    {
        $this->oppNivTerrit = $oppNivTerrit;

        return $this;
    }

    public function getOppValo(): ?string
    {
        return $this->oppValo;
    }

    public function setOppValo(?string $oppValo): self
    {
        $this->oppValo = $oppValo;

        return $this;
    }

    public function getOppPoids(): ?int
    {
        return $this->oppPoids;
    }

    public function setOppPoids(?int $oppPoids): self
    {
        $this->oppPoids = $oppPoids;

        return $this;
    }

    public function getOppPorteurOpp(): ?PorteurOpp
    {
        return $this->oppPorteurOpp;
    }

    public function setOppPorteurOpp(?PorteurOpp $oppPorteurOpp): self
    {
        $this->oppPorteurOpp = $oppPorteurOpp;

        return $this;
    }

    public function getOppParticipative(): ?bool
    {
        return $this->oppParticipative;
    }

    public function setOppParticipative(?bool $oppParticipative): self
    {
        $this->oppParticipative = $oppParticipative;

        return $this;
    }

}
