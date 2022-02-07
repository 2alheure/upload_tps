<?php

namespace App\Entity;

use App\Repository\RenderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RenderRepository::class)
 */
class Render {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Promo::class, inversedBy="renders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $promo;

    /**
     * @ORM\ManyToOne(targetEntity=Exercice::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $exercice;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateBegin;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     * @ORM\OneToMany(targetEntity=Upload::class, mappedBy="render", orphanRemoval=true)
     */
    private $uploads;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $directory;

    public function __construct() {
        $this->uploads = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getPromo(): ?Promo {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): self {
        $this->promo = $promo;

        return $this;
    }

    public function getExercice(): ?Exercice {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): self {
        $this->exercice = $exercice;

        return $this;
    }

    public function getDateBegin(): ?\DateTimeInterface {
        return $this->dateBegin;
    }

    public function setDateBegin(?\DateTimeInterface $dateBegin): self {
        $this->dateBegin = $dateBegin;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return Collection|Upload[]
     */
    public function getUploads(): Collection {
        return $this->uploads;
    }

    public function addUpload(Upload $upload): self {
        if (!$this->uploads->contains($upload)) {
            $this->uploads[] = $upload;
            $upload->setRender($this);
        }

        return $this;
    }

    public function removeUpload(Upload $upload): self {
        if ($this->uploads->removeElement($upload)) {
            // set the owning side to null (unless already changed)
            if ($upload->getRender() === $this) {
                $upload->setRender(null);
            }
        }

        return $this;
    }

    public function getUploadOf(User $user): ?Upload {
        return ($u = $this->getUploads()->filter(function (Upload $upload) use ($user) {
            return $upload->getUser()->getId() === $user->getId();
        })->first()) ? $u : null;
    }

    public function hasUploadOf(User $user): bool {
        return !empty($this->getUploadOf($user));
    }

    public function isOpen(): bool {
        return (!$this->dateBegin || $this->dateBegin->getTimestamp() < time())
            && (!$this->dateEnd || $this->dateEnd->getTimestamp() > time());
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }
}
