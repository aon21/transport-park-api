<?php

namespace App\Controller;

use App\Dto\Request\DriverCreateRequest;
use App\Dto\Request\DriverUpdateRequest;
use App\Dto\Response\DriverResource;
use App\Entity\Driver;
use App\Repository\DriverRepository;
use App\Service\DriverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/drivers', name: 'api_drivers_')]
class DriverController extends AbstractController
{
    public function __construct(
        private readonly DriverRepository $driverRepository,
        private readonly DriverService $driverService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $drivers = $this->driverRepository->findAll();
        
        return $this->json(DriverResource::collection($drivers));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Driver $driver): JsonResponse
    {
        return $this->json(DriverResource::fromEntity($driver));
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] DriverCreateRequest $request): JsonResponse
    {
        $driver = $this->driverService->create($request);

        return $this->json(DriverResource::fromEntity($driver), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(Driver $driver, #[MapRequestPayload] DriverUpdateRequest $request): JsonResponse
    {
        $driver = $this->driverService->update($driver, $request);

        return $this->json(DriverResource::fromEntity($driver));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Driver $driver): JsonResponse
    {
        $this->driverService->delete($driver);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

