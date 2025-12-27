<?php

namespace App\Entity;

use App\Repository\EpisodeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTimeInterface;
use DateTime;

//TODO:Ajouter un champs pour les derniers épisodes

#[ORM\Entity(repositoryClass: EpisodeRepository::class)]
#[Vich\Uploadable]
class Episode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 1000)]
    private ?string $synopsis = null;

    #[ORM\Column]
    private ?int $number = null;

     #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'episodes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Season $season = null;

     #[ORM\Column]
     private ?int $duration = null; //modifier Episodetype dans le dossier Form
     
     #[ORM\Column(length: 255, nullable: true)]
     private ?string $poster = null;
 
   
     #[Vich\UploadableField(mapping: 'poster_file', fileNameProperty: 'poster')]
     #[Assert\File(
     maxSize: '1M',
     mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
     )]
     private ?File $posterFile = null; // On ajoute un champ pour stocker temporairement le fichier poster pas de colonne en base de données
 
     #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
     private ?DatetimeInterface $updatedAt = null;

     /**
      * @var Collection<int, Comment>
      */
     #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'episode')]
     private Collection $comments;

     #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
     private ?\DateTimeImmutable $createdAt = null;

     public function __construct()
     {
         $this->comments = new ArrayCollection();
     }
 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): static
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }
    public function getSeason(): ?Season
    {
        return $this->season;
    }
    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
    public function getPoster(): ?string
    {
        return $this->poster;
    }
    public function setPoster(?string $poster): self
    {
        $this->poster = $poster;

        return $this;
    }
    public function getPosterFile(): ?File
    {
        return $this->posterFile;
    }
    public function setPosterFile(File $image = null): Episode
    {
        $this->posterFile = $image;
        if ($image) {
            $this->updatedAt = new DateTime('now');
        }

        return $this;
    }
    public function getUpdatedAt(): ?DatetimeInterface
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(DatetimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setEpisode($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEpisode() === $this) {
                $comment->setEpisode(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

}
