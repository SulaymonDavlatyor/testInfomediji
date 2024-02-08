<?php

namespace App\Exception;

use App\Entity\Task;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use RuntimeException;
use Throwable;

class ContentException extends RuntimeException
{
    const CONTENT_ALREADY_RELEASED = "Content has already been released at %d";
    const USER_HAS_NO_TASKS = "You have no tasks";

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function contentAlreadyReleased(DateTime $releaseDate): ContentException
    {
        $message = sprintf(self::CONTENT_ALREADY_RELEASED, $releaseDate);
        return new ContentException($message, Response::HTTP_I_AM_A_TEAPOT);
    }
}