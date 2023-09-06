<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LRessourceFileManager
 *
 * @ORM\Table(name="l_ressource_file_manager", indexes={@ORM\Index(name="fki_l_refm_file_manager_id", columns={"l_refm_file_manager_id"}), @ORM\Index(name="fki_l_refm_ressource_id", columns={"l_refm_ressource_id"})})
 * @ORM\Entity
 */
class LRessourceFileManager
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_refm_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_ressource_file_manager_l_refm_id_seq", allocationSize=1, initialValue=1)
     */
    private $lRefmId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_fichier", type="string", length=255, nullable=true)
     */
    private $nomFichier;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_refm_file_manager_id", referencedColumnName="file_manager_id")
     * })
     */
    private $lRefmFileManager;

    /**
     * @var \Ressource
     *
     * @ORM\ManyToOne(targetEntity="Ressource")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_refm_ressource_id", referencedColumnName="ressource_id")
     * })
     */
    private $lRefmRessource;

    public function getLRefmId(): ?int
    {
        return $this->lRefmId;
    }

    public function getNomFichier(): ?string
    {
        return $this->nomFichier;
    }

    public function setNomFichier(?string $nomFichier): self
    {
        $this->nomFichier = $nomFichier;

        return $this;
    }

    public function getLRefmFileManager(): ?FileManager
    {
        return $this->lRefmFileManager;
    }

    public function setLRefmFileManager(?FileManager $lRefmFileManager): self
    {
        $this->lRefmFileManager = $lRefmFileManager;

        return $this;
    }

    public function getLRefmRessource(): ?Ressource
    {
        return $this->lRefmRessource;
    }

    public function setLRefmRessource(?Ressource $lRefmRessource): self
    {
        $this->lRefmRessource = $lRefmRessource;

        return $this;
    }


}
