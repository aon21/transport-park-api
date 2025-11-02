<?php

namespace App\Controller;

use App\Dto\Response\DriverResource;
use App\Service\DriverService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/drivers', name: 'api_drivers_')]
class DriverController extends AbstractController
{
    public function __construct(
        private DriverService $driverService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $unassigned = $request->query->get('unassigned');
        $drivers = $unassigned === 'true' 
            ? $this->driverService->findUnassigned() 
            : $this->driverService->findAll();
        
        return $this->json(DriverResource::collection($drivers));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $driver = $this->driverService->findById($id);
        if (!$driver) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(DriverResource::fromEntity($driver));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $driver = $this->driverService->create($data);

        return $this->json(DriverResource::fromEntity($driver), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $driver = $this->driverService->findById($id);
        if (!$driver) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $driver = $this->driverService->update($driver, $data);

        return $this->json(DriverResource::fromEntity($driver));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $driver = $this->driverService->findById($id);
        if (!$driver) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->driverService->delete($driver);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

