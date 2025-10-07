<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $minimumNumberOfPlayers = null;

    #[ORM\Column(nullable: true)]
    private ?int $maximumNumberOfPlayers = null;

    #[ORM\Column]
    private ?int $averageGameDuration = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $imageFileName = "no-image.svg";

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'games')]
    private Collection $events;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    public function __construct()
    {
        $this->events = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMinimumNumberOfPlayers(): ?int
    {
        return $this->minimumNumberOfPlayers;
    }

    public function setMinimumNumberOfPlayers(int $minimumNumberOfPlayers): static
    {
        $this->minimumNumberOfPlayers = $minimumNumberOfPlayers;

        return $this;
    }

    public function getMaximumNumberOfPlayers(): ?int
    {
        return $this->maximumNumberOfPlayers;
    }

    public function setMaximumNumberOfPlayers(?int $maximumNumberOfPlayers): static
    {
        $this->maximumNumberOfPlayers = $maximumNumberOfPlayers;

        return $this;
    }

    public function getAverageGameDuration(): ?int
    {
        return $this->averageGameDuration;
    }

    public function setAverageGameDuration(int $averageGameDuration): static
    {
        $this->averageGameDuration = $averageGameDuration;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): static
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addGame($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            $event->removeGame($this);
        }

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
}
