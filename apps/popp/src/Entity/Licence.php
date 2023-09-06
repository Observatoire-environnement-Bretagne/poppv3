<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Licence
 *
 * @ORM\Table(name="licence")
 * @ORM\Entity
 */
class Licence
{
    /**
     * @var int
     *
     * @ORM\Column(name="licence_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="licence_licence_id_seq", allocationSize=1, initialValue=1)
     */
    private $licenceId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="licence_nom", type="string", length=255, nullable=true)
     */
    private $licenceNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="licence_desc", type="text", nullable=true)
     */
    private $licenceDesc;

    /**
     * @var int|null
     *
     * @ORM\Column(name="licence_poids", type="integer", nullable=true)
     */
    private $licencePoids;

    public function getLicenceId(): ?int
    {
        return $this->licenceId;
    }

    public function getLicenceNom(): ?string
    {
        return $this->licenceNom;
    }

    public function setLicenceNom(?string $licenceNom): self
    {
        $this->licenceNom = $licenceNom;

        return $this;
    }

    public function getLicenceDesc(): ?string
    {
        return $this->licenceDesc;
    }

    public function setLicenceDesc(?string $licenceDesc): self
    {
        $this->licenceDesc = $licenceDesc;

        return $this;
    }

    public function getLicencePoids(): ?int
    {
        return $this->licencePoids;
    }

    public function setLicencePoids(?int $licencePoids): self
    {
        $this->licencePoids = $licencePoids;

        return $this;
    }


}
