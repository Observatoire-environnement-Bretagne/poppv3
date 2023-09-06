<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileManager
 *
 * @ORM\Table(name="file_manager")
 * @ORM\Entity
 */
class FileManager
{
    /**
     * @var int
     *
     * @ORM\Column(name="file_manager_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="file_manager_file_manager_id_seq", allocationSize=1, initialValue=1)
     */
    private $fileManagerId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_manager_nom", type="string", length=255, nullable=true)
     */
    private $fileManagerNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_manager_uri", type="string", length=255, nullable=true)
     */
    private $fileManagerUri;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_manager_mime", type="string", length=255, nullable=true)
     */
    private $fileManagerMime;

    /**
     * @var int|null
     *
     * @ORM\Column(name="file_manager_statut", type="integer", nullable=true)
     */
    private $fileManagerStatut;

    /**
     * @var int|null
     *
     * @ORM\Column(name="file_manager_size", type="bigint", nullable=true)
     */
    private $fileManagerSize;

    /**
     * @var int|null
     *
     * @ORM\Column(name="file_manager_date", type="bigint", nullable=true)
     */
    private $fileManagerDate;

    

    public function getFileManagerId(): ?int
    {
        return $this->fileManagerId;
    }

    public function getFileManagerNom(): ?string
    {
        return $this->fileManagerNom;
    }

    public function setFileManagerNom(?string $fileManagerNom): self
    {
        $this->fileManagerNom = $fileManagerNom;

        return $this;
    }

    public function getFileManagerUri(): ?string
    {
        return $this->fileManagerUri;
    }

    public function setFileManagerUri(?string $fileManagerUri): self
    {
        $this->fileManagerUri = $fileManagerUri;

        return $this;
    }

    public function getFileManagerMime(): ?string
    {
        return $this->fileManagerMime;
    }

    public function setFileManagerMime(?string $fileManagerMime): self
    {
        $this->fileManagerMime = $fileManagerMime;

        return $this;
    }

    public function getFileManagerStatut(): ?int
    {
        return $this->fileManagerStatut;
    }

    public function setFileManagerStatut(?int $fileManagerStatut): self
    {
        $this->fileManagerStatut = $fileManagerStatut;

        return $this;
    }

    public function getFileManagerSize(): ?string
    {
        return $this->fileManagerSize;
    }

    public function setFileManagerSize(?string $fileManagerSize): self
    {
        $this->fileManagerSize = $fileManagerSize;

        return $this;
    }

    public function getFileManagerDate(): ?string
    {
        return $this->fileManagerDate;
    }

    public function setFileManagerDate(?string $fileManagerDate): self
    {
        $this->fileManagerDate = $fileManagerDate;

        return $this;
    }

}
