<?php

namespace App\Controller;

use App\Dto\Request\TrailerCreateRequest;
use App\Dto\Request\TrailerUpdateRequest;
use App\Dto\Response\TrailerResource;
use App\Dto\TrailerCreateDto;
use App\Dto\TrailerUpdateDto;
use App\Entity\Trailer;
use App\Repository\TrailerRepository;
use App\Service\TrailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/trailers', name: 'api_trailers_')]
class TrailerController extends AbstractController
{
    public function __construct(
        private readonly TrailerRepository $trailerRepository,
        private readonly TrailerService $trailerService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $trailers = $this->trailerRepository->findAll();
        
        return $this->json(TrailerResource::collection($trailers));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Trailer $trailer): JsonResponse
    {
        return $this->json(TrailerResource::fromEntity($trailer));
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] TrailerCreateRequest $request): JsonResponse
    {
        $dto = new TrailerCreateDto(
            registrationNumber: $request->registrationNumber,
            type: $request->type,
            capacity: $request->capacity,
            status: $request->status
        );

        $trailer = $this->trailerService->create($dto);

        return $this->json(TrailerResource::fromEntity($trailer), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Trailer $trailer, #[MapRequestPayload] TrailerUpdateRequest $request): JsonResponse
    {
        $dto = new TrailerUpdateDto(
            registrationNumber: $request->registrationNumber,
            type: $request->type,
            capacity: $request->capacity,
            status: $request->status
        );

        $trailer = $this->trailerService->update($trailer, $dto);

        return $this->json(TrailerResource::fromEntity($trailer));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Trailer $trailer): JsonResponse
    {
        $this->trailerService->delete($trailer);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

