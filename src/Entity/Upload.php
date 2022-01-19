<?php

namespace App\Entity;

use App\Repository\UploadRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UploadRepository::class)
 */
class Upload {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="uploads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Render::class, inversedBy="uploads")
     * @ORM\JoinColumn(nullable=false)
     */
    private $render;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $renderFile;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $renderLink;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): self {
        $this->user = $user;

        return $this;
    }

    public function getRender(): ?Render {
        return $this->render;
    }

    public function setRender(?Render $render): self {
        $this->render = $render;

        return $this;
    }

    public function getRenderFile(): ?string {
        return $this->renderFile;
    }

    public function setRenderFile(?string $renderFile): self {
        $this->renderFile = $renderFile;

        return $this;
    }

    public function unsetRenderFile(): self {
        $this->renderFile = null;

        return $this;
    }

    public function getRenderLink(): ?string {
        return $this->renderLink;
    }

    public function setRenderLink(?string $renderLink): self {
        $this->renderLink = $renderLink;

        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): self {
        $this->comment = $comment;

        return $this;
    }
}
