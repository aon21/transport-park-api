<?php

namespace App\Controller;

use App\Dto\Request\TruckCreateRequest;
use App\Dto\Request\TruckUpdateRequest;
use App\Dto\Response\TruckResource;
use App\Dto\TruckCreateDto;
use App\Dto\TruckUpdateDto;
use App\Entity\Truck;
use App\Repository\TruckRepository;
use App\Service\TruckService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/trucks', name: 'api_trucks_')]
class TruckController extends AbstractController
{
    public function __construct(
        private readonly TruckRepository $truckRepository,
        private readonly TruckService $truckService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $trucks = $this->truckRepository->findAll();

        return $this->json(TruckResource::collection($trucks));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Truck $uuid): JsonResponse
    {
        return $this->json(TruckResource::fromEntity($uuid));
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] TruckCreateRequest $request): JsonResponse
    {
        $dto = new TruckCreateDto(
            registrationNumber: $request->registrationNumber,
            brand: $request->brand,
            model: $request->model,
            status: $request->status
        );

        $truck = $this->truckService->create($dto);

        return $this->json(TruckResource::fromEntity($truck), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(Truck $uuid, #[MapRequestPayload] TruckUpdateRequest $request): JsonResponse
    {
        $dto = new TruckUpdateDto(
            registrationNumber: $request->registrationNumber,
            brand: $request->brand,
            model: $request->model,
            status: $request->status
        );

        $truck = $this->truckService->update($uuid, $dto);

        return $this->json(TruckResource::fromEntity($truck));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Truck $uuid): JsonResponse
    {
        $this->truckService->delete($uuid);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
