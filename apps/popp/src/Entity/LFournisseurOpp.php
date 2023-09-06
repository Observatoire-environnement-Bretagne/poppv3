<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LFournisseurOpp
 *
 * @ORM\Table(name="l_fournisseur_opp", indexes={@ORM\Index(name="fki_l_fo_users_id", columns={"l_fo_users_id"}), @ORM\Index(name="fki_l_fo_opp_id", columns={"l_fo_opp_id"})})
 * @ORM\Entity
 */
class LFournisseurOpp
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_fo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_fournisseur_opp_l_fo_id_seq", allocationSize=1, initialValue=1)
     */
    private $lFoId;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_fo_users_id", referencedColumnName="id")
     * })
     */
    private $lFoUsers;

    /**
     * @var \Opp
     *
     * @ORM\ManyToOne(targetEntity="Opp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_fo_opp_id", referencedColumnName="opp_id")
     * })
     */
    private $lFoOpp;

    public function getLFoId(): ?int
    {
        return $this->lFoId;
    }

    public function getLFoUsers(): ?Users
    {
        return $this->lFoUsers;
    }

    public function setLFoUsers(?Users $lFoUsers): self
    {
        $this->lFoUsers = $lFoUsers;

        return $this;
    }

    public function getLFoOpp(): ?Opp
    {
        return $this->lFoOpp;
    }

    public function setLFoOpp(?Opp $lFoOpp): self
    {
        $this->lFoOpp = $lFoOpp;

        return $this;
    }


}
