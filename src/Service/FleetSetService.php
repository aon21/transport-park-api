<?php

namespace App\Service;

use App\Dto\Request\FleetSetCreateRequest;
use App\Dto\Request\FleetSetUpdateRequest;
use App\Entity\FleetSet;
use App\Repository\TruckRepository;
use App\Repository\TrailerRepository;
use App\Repository\FleetSetRepository;

readonly class FleetSetService
{
    public function __construct(
        private TruckRepository    $truckRepository,
        private TrailerRepository  $trailerRepository,
        private FleetSetRepository $fleetSetRepository,
    ) {}

    public function create(FleetSetCreateRequest $dto): FleetSet
    {
        $truck = $this->truckRepository->findOrFail($dto->truckId);
        $trailer = $this->trailerRepository->findOrFail($dto->trailerId);

        $fleetSet = (new FleetSet())
            ->setName($dto->name)
            ->setTruck($truck)
            ->setTrailer($trailer);

        $this->fleetSetRepository->save($fleetSet, true);

        return $fleetSet;
    }

    public function update(FleetSet $fleetSet, FleetSetUpdateRequest $dto): FleetSet
    {
        $this->updateName($fleetSet, $dto->name);
        $this->updateTruck($fleetSet, $dto->truckId);
        $this->updateTrailer($fleetSet, $dto->trailerId);

        $this->fleetSetRepository->save($fleetSet, true);

        return $fleetSet;
    }

    private function updateName(FleetSet $fleetSet, ?string $name): void
    {
        if ($name !== null) {
            $fleetSet->setName($name);
        }
    }

    private function updateTruck(FleetSet $fleetSet, ?string $truckId): void
    {
        if ($truckId === null) {
            return;
        }

        $truck = $this->truckRepository->findOrFail($truckId);
        $fleetSet->setTruck($truck);
    }

    private function updateTrailer(FleetSet $fleetSet, ?string $trailerId): void
    {
        if ($trailerId === null) {
            return;
        }

        $trailer = $this->trailerRepository->findOrFail($trailerId);
        $fleetSet->setTrailer($trailer);
    }

    public function delete(FleetSet $fleetSet): void
    {
        $this->fleetSetRepository->remove($fleetSet, true);
    }
}
