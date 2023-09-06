<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Faq
 *
 * @ORM\Table(name="faq")
 * @ORM\Entity
 */
class Faq
{
    /**
     * @var int
     *
     * @ORM\Column(name="faq_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="faq_faq_id_seq", allocationSize=1, initialValue=1)
     */
    private $faqId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_titre", type="text", length=255, nullable=true)
     */
    private $faqTitre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_header", type="text", nullable=true)
     */
    private $faqHeader;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_question", type="text", nullable=true)
     */
    private $faqQuestion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_reponse", type="text", nullable=true)
     */
    private $faqReponse;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="faq_date", type="datetime", nullable=true)
     */
    private $faqDate;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_doc_name", type="text", nullable=true)
     */
    private $faqDocName;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="faq_doc_url", type="text", nullable=true)
     */
    private $faqDocUrl;

    /**
     * @var int|null
     *
     * @ORM\Column(name="faq_num_ordre", type="integer", nullable=true)
     */
    private $faqNumOrdre;

    public function getFaqId(): ?int
    {
        return $this->faqId;
    }

    public function getFaqTitre(): ?string
    {
        return $this->faqTitre;
    }

    public function setFaqTitre(?string $faqTitre): self
    {
        $this->faqTitre = $faqTitre;

        return $this;
    }

    public function getFaqHeader(): ?string
    {
        return $this->faqHeader;
    }

    public function setFaqHeader(?string $faqHeader): self
    {
        $this->faqHeader = $faqHeader;

        return $this;
    }
    
    public function getFaqQuestion(): ?string
    {
        return $this->faqQuestion;
    }

    public function setFaqQuestion(?string $faqQuestion): self
    {
        $this->faqQuestion = $faqQuestion;

        return $this;
    }

    public function getFaqReponse(): ?string
    {
        return $this->faqReponse;
    }

    public function setFaqReponse(?string $faqReponse): self
    {
        $this->faqReponse = $faqReponse;

        return $this;
    }
    
    public function getFaqDate(): ?\DateTimeInterface
    {
        return $this->faqDate;
    }

    public function setFaqDate(?\DateTimeInterface $faqDate): self
    {
        $this->faqDate = $faqDate;

        return $this;
    }
    
    public function getFaqFileManager(): ?FileManager
    {
        return $this->serieFileManager;
    }

    public function getFaqDocName(): ?string
    {
        return $this->faqDocName;
    }

    public function setFaqDocName(?string $faqDocName): self
    {
        $this->faqDocName = $faqDocName;

        return $this;
    }
    
        public function getFaqUrl(): ?string
    {
        return $this->faqDocName;
    }

    public function setFaqUrl(?string $faqDocUrl): self
    {
        $this->faqDocUrl = $faqDocUrl;

        return $this;
    }

    public function getFaqNumOrdre(): ?int
    {
        return $this->faqNumOrdre;
    }

    public function setFaqNumOrdre(?int $faqNumOrdre): self
    {
        $this->faqNumOrdre = $faqNumOrdre;

        return $this;
    }
}
