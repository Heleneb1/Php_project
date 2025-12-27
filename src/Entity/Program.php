<?php

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTimeInterface;
use DateTime;



#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[Vich\Uploadable]
#[UniqueEntity('title', message: 'Ce titre existe déjà')]
class Program
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le titre ne doit pas être vide')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre saisi {{ value }} est trop long, il ne devrait pas dépasser {{ limit }} caractères'
    )]
    private ?string $title = null;
    

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le synopsis ne doit pas être vide')]
    #[Assert\Regex(
        pattern: '/plus belle la vie/i',
        match: false,
        message: 'On parle de vraies séries ici'
    )]
    private ?string $synopsis = null;

    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;

  
    #[Vich\UploadableField(mapping: 'poster_file', fileNameProperty: 'poster')]
    #[Assert\File(
    maxSize: '1M',
    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    private ?File $posterFile = null; // On ajoute un champ pour stocker temporairement le fichier poster pas de colonne en base de données

    #[ORM\ManyToOne(inversedBy: 'programs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    /**
     * @var Collection<int, Season>
     */
    #[ORM\OneToMany(targetEntity: Season::class, mappedBy: 'program', orphanRemoval: true)]
    private Collection $seasons;

    /**
     * //si on veut ajouter un acteur à un programme
   * @var Collection<int, Actor>
     */
    #[ORM\ManyToMany(targetEntity: Actor::class, inversedBy: 'programs', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'actor_program')]
    private Collection $actors;
     //si la relation est bidirectionnelle, on doit ajouter les méthodes addActor et removeActor
    //si pas de resultat on doit inverser le mappedBy et inversedBy

    #[ORM\Column(length: 255)]
    private ?string $slug = null;
   
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DatetimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'programs', cascade: ["persist"])]
    private ?User $owner = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'watchlist')]
    private Collection $viewers;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->viewers = new ArrayCollection();
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

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): static
    {
        $this->poster = $poster;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
  /**
 * @return Collection<int, Season>
 */

   
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }
    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons[] = $season;
            $season->setProgram($this);
        }

        return $this;
    }
    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getProgram() === $this) {
                $season->setProgram(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }
    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
            $actor->addProgram($this); // Ajoutez cette ligne
        }
    
        return $this;
    }
    
    public function removeActor(Actor $actor): self
    {
        if ($this->actors->removeElement($actor)) {
            $actor->removeProgram($this); // Ajoutez cette ligne
        }
    
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
    public function setPosterFile(File $image = null): Program
    {
       $this->posterFile = $image;
       if ($image) {
          $this->updatedAt = new DateTime('now');
       }
    
       return $this;
    }
    public function getPosterFile(): ?File
    {
       return $this->posterFile;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getViewers(): Collection
    {
        return $this->viewers;
    }

    public function addViewer(User $viewer): static
    {
        if (!$this->viewers->contains($viewer)) {
            $this->viewers->add($viewer);
            $viewer->addWatchlist($this);
        }

        return $this;
    }

    public function removeViewer(User $viewer): static
    {
        if ($this->viewers->removeElement($viewer)) {
            $viewer->removeWatchlist($this);
        }

        return $this;
    }
}
