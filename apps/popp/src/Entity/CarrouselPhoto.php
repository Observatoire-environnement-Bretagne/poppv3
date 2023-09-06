<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarrouselPhoto
 *
 * @ORM\Table(name="carrousel_photo", indexes={@ORM\Index(name="IDX_66A906DD312A0C2D", columns={"carrousel_photo_file_id"}), @ORM\Index(name="IDX_66A906DD713F7478", columns={"carrousel_actualite_id"})})
 * @ORM\Entity
 */
class CarrouselPhoto
{
    /**
     * @var int
     *
     * @ORM\Column(name="carrousel_photo_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="carrousel_photo_carrousel_photo_id_seq", allocationSize=1, initialValue=1)
     */
    private $carrouselPhotoId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="carrousel_photo_titre", type="string", nullable=true)
     */
    private $carrouselPhotoTitre;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="carrousel_is_creating", type="boolean", nullable=true)
     */
    private $carrouselIsCreating;

    /**
     * @var int|null
     *
     * @ORM\Column(name="carrousel_photo_ordre", type="integer", nullable=true)
     */
    private $carrouselPhotoOrdre;

    /**
     * @var \FileManager
     *
     * @ORM\ManyToOne(targetEntity="FileManager")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carrousel_photo_file_id", referencedColumnName="file_manager_id")
     * })
     */
    private $carrouselPhotoFile;

    /**
     * @var \Actualites
     *
     * @ORM\ManyToOne(targetEntity="Actualites")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="carrousel_actualite_id", referencedColumnName="actualite_id")
     * })
     */
    private $carrouselActualite;

    public function getCarrouselPhotoId(): ?int
    {
        return $this->carrouselPhotoId;
    }

    public function getCarrouselPhotoTitre(): ?string
    {
        return $this->carrouselPhotoTitre;
    }

    public function setCarrouselPhotoTitre(?string $carrouselPhotoTitre): self
    {
        $this->carrouselPhotoTitre = $carrouselPhotoTitre;

        return $this;
    }

    public function getCarrouselIsCreating(): ?bool
    {
        return $this->carrouselIsCreating;
    }

    public function setCarrouselIsCreating(?bool $carrouselIsCreating): self
    {
        $this->carrouselIsCreating = $carrouselIsCreating;

        return $this;
    }

    public function getCarrouselPhotoOrdre(): ?int
    {
        return $this->carrouselPhotoOrdre;
    }

    public function setCarrouselPhotoOrdre(?int $carrouselPhotoOrdre): self
    {
        $this->carrouselPhotoOrdre = $carrouselPhotoOrdre;

        return $this;
    }

    public function getCarrouselPhotoFile(): ?FileManager
    {
        return $this->carrouselPhotoFile;
    }

    public function setCarrouselPhotoFile(?FileManager $carrouselPhotoFile): self
    {
        $this->carrouselPhotoFile = $carrouselPhotoFile;

        return $this;
    }

    public function getCarrouselActualite(): ?Actualites
    {
        return $this->carrouselActualite;
    }

    public function setCarrouselActualite(?Actualites $carrouselActualite): self
    {
        $this->carrouselActualite = $carrouselActualite;

        return $this;
    }


}
