<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ressource
 *
 * @ORM\Table(name="ressource")
 * @ORM\Entity
 */
class Ressource
{
    /**
     * @var int
     * 
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="ressource_id", type="integer", nullable=true)
     */
    private $ressourceId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ressource_titre", type="text", length=255, nullable=true)
     */
    private $ressourceTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ressource_question", type="text", nullable=true)
     */
    private $ressourceQuestion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ressource_desc", type="text", nullable=true)
     */
    private $ressourceDesc;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="ressource_logo_id", referencedColumnName="file_manager_id")
     * })
     */
    private $ressourceLogo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ressource_num_ordre", type="integer", nullable=true)
     */
    private $ressourceNumOrdre;
    
    public function getRessourceId(): ?int
    {
        return $this->ressourceId;
    }

    public function getRessourceTitre(): ?string
    {
        return $this->ressourceTitre;
    }

    public function setRessourceTitre(?string $ressourceTitre): self
    {
        $this->ressourceTitre = $ressourceTitre;

        return $this;
    }

    public function getRessourceQuestion(): ?string
    {
        return $this->ressourceQuestion;
    }

    public function setRessourceQuestion(?string $ressourceQuestion): self
    {
        $this->ressourceQuestion = $ressourceQuestion;

        return $this;
    }

    public function getRessourceDesc(): ?string
    {
        return $this->ressourceDesc;
    }

    public function setRessourceDesc(?string $ressourceDesc): self
    {
        $this->ressourceDesc = $ressourceDesc;

        return $this;
    }
    
    public function getRessourceLogo(): ?FileManager
    {
        return $this->ressourceLogo;
    }

    public function setRessourceLogo(?FileManager $ressourceLogo): self
    {
        $this->ressourceLogo = $ressourceLogo;

        return $this;
    }

    public function getRessourceNumOrdre(): ?int
    {
        return $this->ressourceNumOrdre;
    }

    public function setRessourceNumOrdre(?int $ressourceNumOrdre): self
    {
        $this->ressourceNumOrdre = $ressourceNumOrdre;

        return $this;
    }
}
