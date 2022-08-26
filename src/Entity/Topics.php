<?php

namespace App\Entity;

use App\Repository\TopicsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TopicsRepository::class)]
class Topics
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 3, max: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    #[Assert\Length(min:3, max:128)]
    private $subTitle;

    #[ORM\Column(type: 'text')]
    #[Assert\NotNull()]
    private $mainContent;

    #[ORM\Column(type: 'boolean')]
    private $isActive;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: SubCategories::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private $subCategory;

    #[ORM\ManyToOne(targetEntity: Users::class, inversedBy: 'topics')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\OneToMany(mappedBy: 'topic', targetEntity: TopicResponses::class, orphanRemoval: true)]
    private $topicResponses;

    public function __construct()
    {
        $this->topicResponses = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId()
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

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(?string $subTitle): self
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    public function getMainContent(): ?string
    {
        return $this->mainContent;
    }

    public function setMainContent(string $mainContent): self
    {
        $this->mainContent = $mainContent;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getSubCategory(): ?SubCategories
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategories $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, TopicResponses>
     */
    public function getTopicResponses(): Collection
    {
        return $this->topicResponses;
    }

    public function addTopicResponse(TopicResponses $topicResponse): self
    {
        if (!$this->topicResponses->contains($topicResponse)) {
            $this->topicResponses[] = $topicResponse;
            $topicResponse->setTopic($this);
        }

        return $this;
    }

    public function removeTopicResponse(TopicResponses $topicResponse): self
    {
        if ($this->topicResponses->removeElement($topicResponse)) {
            // set the owning side to null (unless already changed)
            if ($topicResponse->getTopic() === $this) {
                $topicResponse->setTopic(null);
            }
        }

        return $this;
    }
}
