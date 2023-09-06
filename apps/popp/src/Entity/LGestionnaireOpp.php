<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LGestionnaireOpp
 *
 * @ORM\Table(name="l_gestionnaire_opp", indexes={@ORM\Index(name="fki_l_go_users_id", columns={"l_go_users_id"}), @ORM\Index(name="fki_l_go_opp_id", columns={"l_go_opp_id"})})
 * @ORM\Entity
 */
class LGestionnaireOpp
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_go_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_gestionnaire_opp_l_go_id_seq", allocationSize=1, initialValue=1)
     */
    private $lGoId;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_go_users_id", referencedColumnName="id")
     * })
     */
    private $lGoUsers;

    /**
     * @var \Opp
     *
     * @ORM\ManyToOne(targetEntity="Opp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_go_opp_id", referencedColumnName="opp_id")
     * })
     */
    private $lGoOpp;

    public function getLGoId(): ?int
    {
        return $this->lGoId;
    }

    public function getLGoUsers(): ?Users
    {
        return $this->lGoUsers;
    }

    public function setLGoUsers(?Users $lGoUsers): self
    {
        $this->lGoUsers = $lGoUsers;

        return $this;
    }

    public function getLGoOpp(): ?Opp
    {
        return $this->lGoOpp;
    }

    public function setLGoOpp(?Opp $lGoOpp): self
    {
        $this->lGoOpp = $lGoOpp;

        return $this;
    }


}
