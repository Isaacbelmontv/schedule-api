<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete()
    ],
    normalizationContext: [
        'groups' => ['schedule:read'],
        'datetime_format' => 'Y-m-d',
        'time_format' => 'H:i',
    ],
    denormalizationContext: [
        'groups' => ['schedule:write'],
        'datetime_format' => 'Y-m-d',
    ],
    routePrefix: '/api'
)]
#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'schedules')]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Groups(['schedule:read', 'schedule:write'])]
    private ?\DateTimeInterface $day = null;

    /**
     * @param string|\DateTimeInterface $day
     */
    public function setDay($day): self
    {
        if (is_string($day)) {
            $this->day = new \DateTimeImmutable($day);
        } else if ($day instanceof \DateTimeInterface) {
            $this->day = $day;
        } else if ($day !== null) {
            throw new \InvalidArgumentException('Day must be a string or DateTimeInterface');
        }

        return $this;
    }

    #[ORM\Column(type: 'time')]
    #[Assert\NotBlank]
    #[Groups(['schedule:read', 'schedule:write'])]
    private ?\DateTimeInterface $startTime = null;

    /**
     * @param string|\DateTimeInterface $time
     */
    public function setStartTime($time): self
    {
        if (is_string($time)) {
            $this->startTime = \DateTimeImmutable::createFromFormat('H:i', $time);
        } else if ($time instanceof \DateTimeInterface) {
            $this->startTime = $time;
        } else if ($time !== null) {
            throw new \InvalidArgumentException('Start time must be a string in H:i format or DateTimeInterface');
        }

        return $this;
    }

    #[ORM\Column(type: 'time')]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(propertyPath: 'startTime', message: 'End time must be after start time')]
    #[Groups(['schedule:read', 'schedule:write'])]
    private ?\DateTimeInterface $endTime = null;

    /**
     * @param string|\DateTimeInterface $time
     */
    public function setEndTime($time): self
    {
        if (is_string($time)) {
            $this->endTime = \DateTimeImmutable::createFromFormat('H:i', $time);
        } else if ($time instanceof \DateTimeInterface) {
            $this->endTime = $time;
        } else if ($time !== null) {
            throw new \InvalidArgumentException('End time must be a string in H:i format or DateTimeInterface');
        }

        return $this;
    }


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['schedule:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['schedule:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?\DateTimeInterface
    {
        return $this->day;
    }

    /**
     * @Groups({"schedule:read"})
     */
    public function getStartTime(): ?string
    {
        return $this->startTime ? $this->startTime->format('H:i') : null;
    }

    /**
     * @Groups({"schedule:read"})
     */
    public function getEndTime(): ?string
    {
        return $this->endTime ? $this->endTime->format('H:i') : null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
}
