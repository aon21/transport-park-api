<?php

namespace App\Service;

use App\Entity\Trailer;
use App\Repository\TrailerRepository;

class TrailerService
{
    public function __construct(
        private TrailerRepository $trailerRepository
    ) {
    }

    public function findAll(?string $status = null, ?string $type = null): array
    {
        if ($status) {
            return $this->trailerRepository->findByStatus($status);
        }
        if ($type) {
            return $this->trailerRepository->findByType($type);
        }
        return $this->trailerRepository->findAll();
    }

    public function findById(string $id): ?Trailer
    {
        return $this->trailerRepository->find($id);
    }

    public function create(array $data): Trailer
    {
        $trailer = new Trailer();
        $trailer->setRegistrationNumber($data['registrationNumber'])
            ->setType($data['type'])
            ->setCapacity($data['capacity'])
            ->setStatus($data['status']);

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    public function update(Trailer $trailer, array $data): Trailer
    {
        if (isset($data['registrationNumber'])) {
            $trailer->setRegistrationNumber($data['registrationNumber']);
        }
        if (isset($data['type'])) {
            $trailer->setType($data['type']);
        }
        if (isset($data['capacity'])) {
            $trailer->setCapacity($data['capacity']);
        }
        if (isset($data['status'])) {
            $trailer->setStatus($data['status']);
        }

        $this->trailerRepository->save($trailer, true);

        return $trailer;
    }

    public function delete(Trailer $trailer): void
    {
        $this->trailerRepository->remove($trailer, true);
    }
}

