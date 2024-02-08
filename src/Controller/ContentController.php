<?php

namespace App\Controller;

use App\Dto\CreateContentDto;
use App\Dto\EditContentDto;
use App\Service\ContentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ContentController extends AbstractController
{

    public function __construct(private Security $security, private ContentService $contentService){

    }

    #[Route('/content', name: 'app_content_create',methods: 'POST')]
    public function create(#[MapRequestPayload] CreateContentDto $dto)
    {
        $userId = $this->security->getUser()->getId();
        $dto->setUserId($userId);
        $task = $this->contentService->createContent($dto);

        return new Response(json_encode($task),200);
    }

    #[Route('/content/{id}', name: 'app_content_edit', methods: 'PUT')]
    public function edit(
        Request $request,
        #[MapRequestPayload] EditContentDto $dto
    ) {
        $userId = $this->security->getUser()->getId();
        $dto->setId($request->get('id'));
        $dto->setUserId($userId);

        $task = $this->contentService->editContent($dto);

        return new Response(json_encode($task),200);
    }
}
