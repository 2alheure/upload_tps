<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ExerciceRepository::class)
 */
class Exercice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $subjectFile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $subjectLink;

    /**
     * @ORM\ManyToOne(targetEntity=Module::class, inversedBy="exercices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $module;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubjectFile(): ?string
    {
        return $this->subjectFile;
    }

    public function setSubjectFile(string $subjectFile): self
    {
        $this->subjectFile = $subjectFile;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getSubjectLink(): ?string
    {
        return $this->subjectLink;
    }

    public function setSubjectLink(?string $subjectLink): self
    {
        $this->subjectLink = $subjectLink;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
