<?php

namespace App\Controller;

use App\Dto\Response\TruckResource;
use App\Service\TruckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/trucks', name: 'api_trucks_')]
class TruckController extends AbstractController
{
    public function __construct(
        private TruckService $truckService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $trucks = $this->truckService->findAll($status);
        
        return $this->json(TruckResource::collection($trucks));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $truck = $this->truckService->findById($id);
        if (!$truck) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(TruckResource::fromEntity($truck));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $truck = $this->truckService->create($data);

        return $this->json(TruckResource::fromEntity($truck), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $truck = $this->truckService->findById($id);
        if (!$truck) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $truck = $this->truckService->update($truck, $data);

        return $this->json(TruckResource::fromEntity($truck));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $truck = $this->truckService->findById($id);
        if (!$truck) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->truckService->delete($truck);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
