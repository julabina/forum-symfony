<?php

namespace App\Entity;

use App\Repository\SubCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubCategoriesRepository::class)]
class SubCategories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 255)]
    private $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotNull()]
    private $description;

    #[ORM\ManyToOne(targetEntity: Categories::class, inversedBy: 'subCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\OneToMany(mappedBy: 'subCategory', targetEntity: Topics::class, orphanRemoval: true)]
    private $topics;

    public function __construct()
    {
        $this->topics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Topics>
     */
    public function getTopics(): Collection
    {
        return $this->topics;
    }

    public function addTopic(Topics $topic): self
    {
        if (!$this->topics->contains($topic)) {
            $this->topics[] = $topic;
            $topic->setSubCategory($this);
        }

        return $this;
    }

    public function removeTopic(Topics $topic): self
    {
        if ($this->topics->removeElement($topic)) {
            // set the owning side to null (unless already changed)
            if ($topic->getSubCategory() === $this) {
                $topic->setSubCategory(null);
            }
        }

        return $this;
    }
}
