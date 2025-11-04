<?php

namespace App\Controller;

use App\Dto\Request\FleetSetCreateRequest;
use App\Dto\Request\FleetSetUpdateRequest;
use App\Dto\Response\FleetSetResource;
use App\Dto\Response\FleetStatisticsResponse;
use App\Entity\FleetSet;
use App\Repository\FleetSetRepository;
use App\Service\FleetSetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/fleet-sets', name: 'api_fleet_sets_')]
class FleetSetController extends AbstractController
{
    public function __construct(
        private readonly FleetSetRepository $fleetSetRepository,
        private readonly FleetSetService    $fleetSetService
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $fleetSets = $this->fleetSetRepository->findAllWithRelations();

        return $this->json(FleetSetResource::collection($fleetSets));
    }

    #[Route('/statistics', methods: ['GET'])]
    public function statistics(): JsonResponse
    {
        $stats = $this->fleetSetRepository->getFleetStatistics();

        return $this->json(FleetStatisticsResponse::fromArray($stats));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(FleetSet $fleetSet): JsonResponse
    {
        return $this->json(FleetSetResource::fromEntity($fleetSet));
    }

    #[Route('', methods: ['POST'])]
    public function create(#[MapRequestPayload] FleetSetCreateRequest $request): JsonResponse
    {
        $fleetSet = $this->fleetSetService->create($request);

        return $this->json(
            FleetSetResource::fromEntity($fleetSet),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(FleetSet $fleetSet, #[MapRequestPayload] FleetSetUpdateRequest $request): JsonResponse
    {
        $updatedFleetSet = $this->fleetSetService->update($fleetSet, $request);

        return $this->json(FleetSetResource::fromEntity($updatedFleetSet));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(FleetSet $fleetSet): JsonResponse
    {
        $this->fleetSetService->delete($fleetSet);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
