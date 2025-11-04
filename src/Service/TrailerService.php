<?php

namespace App\Service;

use App\Dto\Request\TrailerCreateRequest;
use App\Dto\Request\TrailerUpdateRequest;
use App\Entity\Trailer;
use App\Repository\TrailerRepository;

readonly class TrailerService
{
    public function __construct(
        private TrailerRepository $trailerRepository
    ) {
    }

    public function create(TrailerCreateRequest $dto): Trailer
    {
        $trailer = (new Trailer())
            ->setRegistrationNumber($dto->registrationNumber)
            ->setType($dto->type)
            ->setCapacity($dto->capacity)
            ->setStatus($dto->status);

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    public function update(Trailer $trailer, TrailerUpdateRequest $dto): Trailer
    {
        $this->updateRegistrationNumber($trailer, $dto->registrationNumber);
        $this->updateType($trailer, $dto->type);
        $this->updateCapacity($trailer, $dto->capacity);
        $this->updateStatus($trailer, $dto->status);

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    private function updateRegistrationNumber(Trailer $trailer, ?string $registrationNumber): void
    {
        if ($registrationNumber !== null) {
            $trailer->setRegistrationNumber($registrationNumber);
        }
    }

    private function updateType(Trailer $trailer, ?string $type): void
    {
        if ($type !== null) {
            $trailer->setType($type);
        }
    }

    private function updateCapacity(Trailer $trailer, ?string $capacity): void
    {
        if ($capacity !== null) {
            $trailer->setCapacity($capacity);
        }
    }

    private function updateStatus(Trailer $trailer, ?string $status): void
    {
        if ($status !== null) {
            $trailer->setStatus($status);
        }
    }

    public function delete(Trailer $trailer): void
    {
        $this->trailerRepository->remove($trailer, true);
    }
}

