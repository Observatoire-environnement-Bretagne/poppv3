<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Apropos
 *
 * @ORM\Table(name="apropos")
 * @ORM\Entity
 */
class Apropos
{
    /**
     * @var int
     *
     * @ORM\Column(name="apropos_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="apropos_apropos_id_seq", allocationSize=1, initialValue=1)
     */
    private $aproposId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apropos_titre", type="text", length=255, nullable=true)
     */
    private $aproposTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apropos_description", type="text", nullable=true)
     */
    private $aproposDescription;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="apropos_doc_url", type="text", nullable=true)
     */
    private $aproposDocUrl;   
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="apropos_doc_label", type="text", nullable=true)
     */
    private $aproposDocLabel;

    /**
     * @var int|null
     *
     * @ORM\Column(name="apropos_num_ordre", type="integer", nullable=true)
     */
    private $aproposNumOrdre;


    public function getAproposId(): ?int
    {
        return $this->aproposId;
    }

    public function getAproposTitre(): ?string
    {
        return $this->aproposTitre;
    }

    public function setAproposTitre(?string $aproposTitre): self
    {
        $this->aproposTitre = $aproposTitre;

        return $this;
    }

    public function getAproposDescription(): ?string
    {
        return $this->aproposDescription;
    }

    public function setAproposDescription(?string $aproposDescription): self
    {
        $this->aproposDescription = $aproposDescription;

        return $this;
    }
    
    public function getAproposDocUrl(): ?string
    {
        return $this->aproposDocUrl;
    }

    public function setAproposDocUrl(?string $aproposDocUrl): self
    {
        $this->aproposDocUrl = $aproposDocUrl;

        return $this;
    }
        
    public function getAproposDocLabel(): ?string
    {
        return $this->aproposDocLabel;
    }

    public function setAproposDocLabel(?string $aproposDocLabel): self
    {
        $this->aproposDocLabel = $aproposDocLabel;

        return $this;
    }

    public function getAproposNumOrdre(): ?int
    {
        return $this->aproposNumOrdre;
    }

    public function setAproposNumOrdre(?int $aproposNumOrdre): self
    {
        $this->aproposNumOrdre = $aproposNumOrdre;

        return $this;
    }
}
