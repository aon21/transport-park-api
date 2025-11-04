<?php

namespace App\Service;

use App\Dto\FleetSetCreateDto;
use App\Dto\FleetSetUpdateDto;
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

    public function create(FleetSetCreateDto $dto): FleetSet
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

    public function update(FleetSet $fleetSet, FleetSetUpdateDto $dto): FleetSet
    {
        $truck = $this->truckRepository->findOrFail($dto->truckId);
        $trailer = $this->trailerRepository->findOrFail($dto->trailerId);

        $fleetSet
            ->setName($dto->name)
            ->setTruck($truck)
            ->setTrailer($trailer);

        $this->fleetSetRepository->save($fleetSet, true);

        return $fleetSet;
    }

    public function delete(FleetSet $fleetSet): void
    {
        $this->fleetSetRepository->remove($fleetSet, true);
    }
}
