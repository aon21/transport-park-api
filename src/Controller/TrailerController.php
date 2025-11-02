<?php

namespace App\Controller;

use App\Dto\Response\TrailerResource;
use App\Service\TrailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/trailers', name: 'api_trailers_')]
class TrailerController extends AbstractController
{
    public function __construct(
        private TrailerService $trailerService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $type = $request->query->get('type');
        $trailers = $this->trailerService->findAll($status, $type);
        
        return $this->json(TrailerResource::collection($trailers));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $trailer = $this->trailerService->findById($id);
        if (!$trailer) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(TrailerResource::fromEntity($trailer));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $trailer = $this->trailerService->create($data);

        return $this->json(TrailerResource::fromEntity($trailer), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $trailer = $this->trailerService->findById($id);
        if (!$trailer) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $trailer = $this->trailerService->update($trailer, $data);

        return $this->json(TrailerResource::fromEntity($trailer));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $trailer = $this->trailerService->findById($id);
        if (!$trailer) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->trailerService->delete($trailer);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

