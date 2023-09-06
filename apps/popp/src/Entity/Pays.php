<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pays
 *
 * @ORM\Table(name="pays")
 * @ORM\Entity
 */
class Pays
{
    /**
     * @var int
     *
     * @ORM\Column(name="pays_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="pays_pays_id_seq", allocationSize=1, initialValue=1)
     */
    private $paysId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pays_nom", type="string", length=255, nullable=true)
     */
    private $paysNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="pays_desc", type="text", nullable=true)
     */
    private $paysDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="pays_poids", type="integer", nullable=true)
     */
    private $paysPoids;

    public function getPaysId(): ?int
    {
        return $this->paysId;
    }

    public function getPaysNom(): ?string
    {
        return $this->paysNom;
    }

    public function setPaysNom(?string $paysNom): self
    {
        $this->paysNom = $paysNom;

        return $this;
    }

    public function getPaysDesc(): ?string
    {
        return $this->paysDesc;
    }

    public function setPaysDesc(?string $paysDesc): self
    {
        $this->paysDesc = $paysDesc;

        return $this;
    }

    public function getPaysPoids(): ?int
    {
        return $this->paysPoids;
    }

    public function setPaysPoids(?int $paysPoids): self
    {
        $this->paysPoids = $paysPoids;

        return $this;
    }


}
