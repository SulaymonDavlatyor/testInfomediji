<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class EditContentDto
{
    private int $id;

    private int $userId;

    private string $title;

    private string $description;

    private DateTime $release_date;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

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


}