<?php

namespace App\Service;

use App\Dto\TrailerCreateDto;
use App\Dto\TrailerUpdateDto;
use App\Entity\Trailer;
use App\Repository\TrailerRepository;

readonly class TrailerService
{
    public function __construct(
        private TrailerRepository $trailerRepository
    ) {
    }

    public function create(TrailerCreateDto $dto): Trailer
    {
        $trailer = (new Trailer())
            ->setRegistrationNumber($dto->registrationNumber)
            ->setType($dto->type)
            ->setCapacity($dto->capacity)
            ->setStatus($dto->status);

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    public function update(Trailer $trailer, TrailerUpdateDto $dto): Trailer
    {
        $trailer
            ->setRegistrationNumber($dto->registrationNumber)
            ->setType($dto->type)
            ->setCapacity($dto->capacity)
            ->setStatus($dto->status);

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    public function delete(Trailer $trailer): void
    {
        $this->trailerRepository->remove($trailer, true);
    }
}

