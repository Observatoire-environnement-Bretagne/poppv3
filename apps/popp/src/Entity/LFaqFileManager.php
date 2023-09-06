<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LFaqFileManager
 *
 * @ORM\Table(name="l_faq_file_manager", indexes={@ORM\Index(name="fki_l_fafm_faq_id", columns={"l_fafm_faq_id"}), @ORM\Index(name="IDX_2A421B93319D1DE", columns={"l_fafm_file_manager_id"})})
 * @ORM\Entity
 */
class LFaqFileManager
{
    /**
     * @var int
     *
     * @ORM\Column(name="l_fafm_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="l_faq_file_manager_l_fafm_id_seq", allocationSize=1, initialValue=1)
     */
    private $lFafmId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="l_fafm_nom_fichier", type="string", nullable=true)
     */
    private $lFafmNomFichier;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_fafm_file_manager_id", referencedColumnName="file_manager_id")
     * })
     */
    private $lFafmFileManager;

    /**
     * @var \Faq
     *
     * @ORM\ManyToOne(targetEntity="Faq")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="l_fafm_faq_id", referencedColumnName="faq_id")
     * })
     */
    private $lFafmFaq;

    public function getLFafmId(): ?int
    {
        return $this->lFafmId;
    }

    public function getLFafmNomFichier(): ?string
    {
        return $this->lFafmNomFichier;
    }

    public function setLFafmNomFichier(?string $lFafmNomFichier): self
    {
        $this->lFafmNomFichier = $lFafmNomFichier;

        return $this;
    }

    public function getLFafmFileManager(): ?FileManager
    {
        return $this->lFafmFileManager;
    }

    public function setLFafmFileManager(?FileManager $lFafmFileManager): self
    {
        $this->lFafmFileManager = $lFafmFileManager;

        return $this;
    }

    public function getLFafmFaq(): ?Faq
    {
        return $this->lFafmFaq;
    }

    public function setLFafmFaq(?Faq $lFafmFaq): self
    {
        $this->lFafmFaq = $lFafmFaq;

        return $this;
    }


}
