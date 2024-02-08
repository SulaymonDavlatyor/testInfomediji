<?php

namespace App\Service;

use App\Dto\CreateContentDto;
use App\Dto\EditContentDto;
use App\Entity\Content;
use App\Exception\ContentException;
use App\Repository\ContentRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class ContentService
{


    public function __construct(
        private ContentRepository $contentRepository,
        private EntityManagerInterface $entityManager,
        private RabbitmqService $rabbitmqService
    ){
    }

    public function getAllUserContent(int $userId): array
    {
        $tasks = $this->contentRepository->findBy(['user_id' => $userId]);
        return $tasks;
    }

    public function createContent(CreateContentDto $dto){
            $content = new Content();

            $content->setUserId($dto->getUserId());
            $content->setDescription($dto->getDescription());
            $content->setTitle($dto->getTitle());
            $content->setReleaseDate($dto->getReleaseDate());
            $content->setVersion(1);

            $this->entityManager->persist($content);
            $this->entityManager->flush();

            $this->rabbitmqService->sendScheduledContent(
                $content->getId(),
                $content->getVersion(),
                $content->getReleaseDate()
            );

            return $content;
    }

    public function editContent(EditContentDto $dto){

        $id = $dto->getId();
        $userId = $dto->getUserId();
        $releaseDate = $dto->getReleaseDate();
        if($releaseDate > new DateTime()){
            ContentException::contentAlreadyReleased($releaseDate);
        }
        $content = $this->contentRepository->findOneBy(['id' => $id, 'userId' => $userId]);

        $content->setDescription($dto->getDescription());
        $content->setTitle($dto->getTitle());
        $content->setReleaseDate($dto->getReleaseDate());
        $content->setVersion($content->getVersion() + 1);

        $this->entityManager->persist($content);
        $this->entityManager->flush();

        //Should have used DTO
        $this->rabbitmqService->sendScheduledContent(
            $content->getId(),
            $content->getVersion(),
            $content->getReleaseDate()
        );

        return $content;
    }

}