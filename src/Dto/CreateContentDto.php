<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateContentDto
{
    #[NotBlank(message: 'Title is missing')]
    #[Type('string')]
    private string $title;

    #[NotBlank(message: 'Description is missing')]
    #[Type('string')]
    private string $description;

    #[NotBlank(message: 'Release date is missing')]
    #[Type('datetime')]
    private DateTime $release_date;
    private int $userId;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getReleaseDate(): DateTime
    {
        return $this->release_date;
    }

    public function setReleaseDate(DateTime $release_date): void
    {
        $this->release_date = $release_date;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }



}