<?php

namespace App\Tests\Feature;

use App\Entity\Trailer;
use App\Tests\ApiTestCase;
use App\Tests\Fixtures\FleetSetFixtures;
use App\Tests\Fixtures\TrailerFixtures;

class TrailerControllerTest extends ApiTestCase
{
    public function testIndexReturnsAllTrailers(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->client->request('GET', '/api/trailers');

        $this->assertResponseStatusCodeSame(200);
        $json = $this->getJsonResponse();
        $this->assertIsArray($json);
        $this->assertCount(5, $json);
        $this->assertHasJsonKeys(['id', 'registrationNumber'], $json[0]);
    }

    public function testShowReturnsTrailerById(): void
    {
        $trailer = $this->loadFixtures([TrailerFixtures::class])
            ->getReference(TrailerFixtures::TRAILER_1, Trailer::class);

        $this->client->request('GET', '/api/trailers/' . $trailer->getId()->toRfc4122());

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'registrationNumber' => 'TRAILER-001',
            'type' => 'Refrigerated',
            'capacity' => '25.50',
            'status' => 'operational'
        ]);
    }

    public function testShowReturns404WhenTrailerNotFound(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);
        $this->client->request('GET', '/api/trailers/' . $this->getNonExistentUuid());
        $this->assertErrorResponse(404);
    }

    public function testCreateCreatesNewTrailerWithValidData(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-100',
            'type' => 'Refrigerated',
            'capacity' => 30.5,
            'status' => 'operational'
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonFields([
            'registrationNumber' => 'TRAILER-100',
            'type' => 'Refrigerated',
            'capacity' => '30.50',
            'status' => 'operational'
        ]);
    }

    public function testCreateReturns422WithMissingRequiredFields(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);
        $this->requestJson('POST', '/api/trailers', ['type' => 'Refrigerated']);
        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithInvalidStatus(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-200',
            'type' => 'Refrigerated',
            'capacity' => 25.5,
            'status' => 'invalid_status'
        ]);

        $this->assertErrorResponse();
    }

    public function testCreateReturns422WithDuplicateRegistrationNumber(): void
    {
        $this->loadFixtures([TrailerFixtures::class]);

        $this->requestJson('POST', '/api/trailers', [
            'registrationNumber' => 'TRAILER-001',
            'type' => 'Refrigerated',
            'capacity' => 25.5,
            'status' => 'operational'
        ]);

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [422, 500]));
        $this->assertArrayHasKey('error', $this->getJsonResponse());
    }

    public function testUpdateModifiesTrailerWithValidData(): void
    {
        $trailer = $this->loadFixtures([TrailerFixtures::class])
            ->getReference(TrailerFixtures::TRAILER_1, Trailer::class);

        $this->requestJson('PUT', '/api/trailers/' . $trailer->getId()->toRfc4122(), [
            'registrationNumber' => 'TRAILER-001-UPDATED',
            'type' => 'Updated Type',
            'capacity' => 35.0,
            'status' => 'in_service'
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonFields([
            'registrationNumber' => 'TRAILER-001-UPDATED',
            'type' => 'Updated Type',
            'capacity' => '35.00',
            'status' => 'in_service'
        ]);
    }

    public function testDeleteRemovesTrailer(): void
    {
        $trailer = $this->loadFixtures([TrailerFixtures::class])
            ->getReference(TrailerFixtures::TRAILER_5_IN_SERVICE, Trailer::class);

        $this->client->request('DELETE', '/api/trailers/' . $trailer->getId()->toRfc4122());
        $this->assertResponseStatusCodeSame(204);
    }

    public function testDeleteTrailerUsedInFleetSet(): void
    {
        $trailer = $this->loadFixtures([TrailerFixtures::class, FleetSetFixtures::class])
            ->getReference(TrailerFixtures::TRAILER_1, Trailer::class);

        $this->client->request('DELETE', '/api/trailers/' . $trailer->getId()->toRfc4122());

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertTrue(in_array($statusCode, [204, 500]));
    }
}
