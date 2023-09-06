<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EvolutionPaysage
 *
 * @ORM\Table(name="evolution_paysage")
 * @ORM\Entity
 */
class EvolutionPaysage
{
    /**
     * @var int
     *
     * @ORM\Column(name="evolution_paysage_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="evolution_paysage_evolution_paysage_id_seq", allocationSize=1, initialValue=1)
     */
    private $evolutionPaysageId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="evolution_paysage_nom", type="string", length=255, nullable=true)
     */
    private $evolutionPaysageNom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="evolution_paysage_nom_lien", type="string", length=255, nullable=true)
     */
    private $evolutionPaysageNomLien;

    public function getEvolutionPaysageId(): ?int
    {
        return $this->evolutionPaysageId;
    }

    public function getEvolutionPaysageNom(): ?string
    {
        return $this->evolutionPaysageNom;
    }

    public function setEvolutionPaysageNom(?string $evolutionPaysageNom): self
    {
        $this->evolutionPaysageNom = $evolutionPaysageNom;

        return $this;
    }

    public function getEvolutionPaysageNomLien(): ?string
    {
        return $this->evolutionPaysageNomLien;
    }

    public function setEvolutionPaysageNomLien(?string $evolutionPaysageNomLien): self
    {
        $this->evolutionPaysageNomLien = $evolutionPaysageNomLien;

        return $this;
    }

}
