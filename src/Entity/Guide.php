<?php

namespace App\Entity;

use App\Repository\GuideRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuideRepository::class)]
class Guide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10000, nullable: true)]
    private ?string $description = null;


    #[ORM\OneToMany(mappedBy: 'guide', cascade:["persist"], targetEntity: PictureGuide::class)]
    private Collection $pictureGuides;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'guide', cascade:["persist"], targetEntity: Trip::class)]
    private Collection $trips;

    

    public function __construct()
    {
        $this->pictureGuides = new ArrayCollection();
        $this->trips = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, PictureGuide>
     */
    public function getPictureGuides(): Collection
    {
        return $this->pictureGuides;
    }

    public function addPictureGuide(PictureGuide $pictureGuide): self
    {
        if (!$this->pictureGuides->contains($pictureGuide)) {
            $this->pictureGuides->add($pictureGuide);
            $pictureGuide->setGuide($this);
        }

        return $this;
    }

    public function removePictureGuide(PictureGuide $pictureGuide): self
    {
        if ($this->pictureGuides->removeElement($pictureGuide)) {
            // set the owning side to null (unless already changed)
            if ($pictureGuide->getGuide() === $this) {
                $pictureGuide->setGuide(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): self
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setGuide($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): self
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getGuide() === $this) {
                $trip->setGuide(null);
            }
        }

        return $this;
    }

   
}
