<?php

namespace App\Entity;

use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;

use DateTime;

#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[Vich\Uploadable]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    /**
 * @var Collection<int, Program>
     */
    #[ORM\ManyToMany(targetEntity: Program::class, mappedBy: 'actors', fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $programs;

    #[ORM\Column(length: 10)]
    private ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

      
    #[Vich\UploadableField(mapping: 'poster_file', fileNameProperty: 'picture')]
    #[Assert\File(
    maxSize: '1M',
    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    private ?File $pictureFile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null; // On ajoute un champ pour stocker temporairement le fichier poster pas de colonne en base de données

    
    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): static
    {
        if (!$this->programs->contains($program)) {
            $this->programs->add($program);
            $program->addActor($this); // Synchronisation bidirectionnelle
        }

        return $this;
    }

    public function removeProgram(Program $program): static
    {
        if ($this->programs->removeElement($program)) {
            $program->removeActor($this); // Synchronisation bidirectionnelle
        }

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }
    public function getPicture(): ?string
        {
    return $this->picture;
    }


    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }
    public function setPicture(string $picture): Actor
    {
        $this->picture = $picture;
        return $this;
    }
    public function setPictureFile(File $image = null): Actor
    {
        $this->pictureFile = $image;
        if ($image) {
            $this->updatedAt = new DateTime('now');
         }

        return $this;
    }
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
   
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
