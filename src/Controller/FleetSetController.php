<?php

namespace App\Controller;

use App\Dto\Response\FleetSetResource;
use App\Repository\FleetSetRepository;
use App\Repository\TruckRepository;
use App\Repository\TrailerRepository;
use App\Service\FleetStatusService;
use App\Entity\FleetSet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/fleet-sets', name: 'api_fleet_sets_')]
class FleetSetController extends AbstractController
{
    public function __construct(
        private FleetSetRepository $fleetSetRepository,
        private TruckRepository $truckRepository,
        private TrailerRepository $trailerRepository,
        private FleetStatusService $fleetStatusService
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
        $fleetSets = $this->fleetSetRepository->findAll();
        $stats = $this->fleetStatusService->getStatistics($fleetSets);
        
        return $this->json($stats);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $fleetSet = $this->fleetSetRepository->find($id);
        if (!$fleetSet) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(FleetSetResource::fromEntity($fleetSet));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $truck = $this->truckRepository->find($data['truckId']);
        $trailer = $this->trailerRepository->find($data['trailerId']);

        if (!$truck || !$trailer) {
            return $this->json(['error' => 'Truck or Trailer not found'], Response::HTTP_BAD_REQUEST);
        }

        $fleetSet = new FleetSet();
        $fleetSet->setName($data['name'])
            ->setTruck($truck)
            ->setTrailer($trailer);

        $this->fleetSetRepository->save($fleetSet, true);

        return $this->json(FleetSetResource::fromEntity($fleetSet), Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $fleetSet = $this->fleetSetRepository->find($id);
        if (!$fleetSet) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $fleetSet->setName($data['name']);
        }
        if (isset($data['truckId'])) {
            $truck = $this->truckRepository->find($data['truckId']);
            if ($truck) {
                $fleetSet->setTruck($truck);
            }
        }
        if (isset($data['trailerId'])) {
            $trailer = $this->trailerRepository->find($data['trailerId']);
            if ($trailer) {
                $fleetSet->setTrailer($trailer);
            }
        }

        $this->fleetSetRepository->save($fleetSet, true);

        return $this->json(FleetSetResource::fromEntity($fleetSet));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $fleetSet = $this->fleetSetRepository->find($id);
        if (!$fleetSet) {
            return $this->json(['error' => 'Not found'], Response::HTTP_NOT_FOUND);
        }

        $this->fleetSetRepository->remove($fleetSet, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

