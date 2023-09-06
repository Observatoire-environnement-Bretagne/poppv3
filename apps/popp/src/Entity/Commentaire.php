<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Commentaire
 *
 * @ORM\Table(name="commentaire", indexes={@ORM\Index(name="fki_commentaire_auteur_id", columns={"commentaire_auteur_id"}), @ORM\Index(name="fki_commentaire_photo_id", columns={"commentaire_photo_id"})})
 * @ORM\Entity
 */
class Commentaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="commentaire_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="commentaire_commentaire_id_seq", allocationSize=1, initialValue=1)
     */
    private $commentaireId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="commentaire_text", type="text", nullable=true)
     */
    private $commentaireText;

    /**
     * @var int|null
     *
     * @ORM\Column(name="commentaire_etat", type="integer", nullable=true)
     */
    private $commentaireEtat;

    /**
     * @var \DateTime|null
     * 
     * @ORM\Column(name="commentaire_date", type="datetime", nullable=true)
     */
    private $commentaireDate;

    /**
     * @var \Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commentaire_auteur_id", referencedColumnName="id")
     * })
     */
    private $commentaireAuteur;

    /**
     * @var \Photo
     *
     * @ORM\ManyToOne(targetEntity="Photo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="commentaire_photo_id", referencedColumnName="photo_id")
     * })
     */
    private $commentairePhoto;

    public function getCommentaireId(): ?int
    {
        return $this->commentaireId;
    }

    public function getCommentaireText(): ?string
    {
        return $this->commentaireText;
    }

    public function setCommentaireText(?string $commentaireText): self
    {
        $this->commentaireText = $commentaireText;

        return $this;
    }

    public function getCommentaireEtat(): ?int
    {
        return $this->commentaireEtat;
    }

    public function setCommentaireEtat(?int $commentaireEtat): self
    {
        $this->commentaireEtat = $commentaireEtat;

        return $this;
    }
    public function getCommentaireDate(): ?\DateTimeInterface
    {
        return $this->commentaireDate;
    }

    public function setCommentaireDate(?\DateTimeInterface $commentaireDate): self
    {
        $this->commentaireDate = $commentaireDate;

        return $this;
    }

    public function getCommentaireAuteur(): ?Users
    {
        return $this->commentaireAuteur;
    }

    public function setCommentaireAuteur(?Users $commentaireAuteur): self
    {
        $this->commentaireAuteur = $commentaireAuteur;

        return $this;
    }

    public function getCommentairePhoto(): ?Photo
    {
        return $this->commentairePhoto;
    }

    public function setCommentairePhoto(?Photo $commentairePhoto): self
    {
        $this->commentairePhoto = $commentairePhoto;

        return $this;
    }


}
