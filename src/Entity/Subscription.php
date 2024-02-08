<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $subscriberId = null;

    #[ORM\Column]
    private ?int $subscribedToId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriberId(): ?int
    {
        return $this->subscriberId;
    }

    public function setSubscriberId(int $subscriberId): static
    {
        $this->subscriberId = $subscriberId;

        return $this;
    }

    public function getSubscribedToId(): ?int
    {
        return $this->subscribedToId;
    }

    public function setSubscribedToId(int $subscribedToId): static
    {
        $this->subscribedToId = $subscribedToId;

        return $this;
    }
}
